<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminLogStored;
use App\Exports\DiamondExport;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmMail;
use App\Mail\HoldMail;
use App\Mail\RequestMail;
use App\Models\DiamondCart;
use App\Models\DiamondConfirm;
use App\Models\DiamondHold;
use App\Models\DiamondInquiry;
use App\Models\DiamondRequest;
use App\Models\Product;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as BaseExcel;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class DiamondController extends Controller
{
    public function slot()
    {
        $Slot = Slot::all();
        return response()->json(['success' => true, 'data' => ['result' => $Slot, 'message' => 'success']], 200);
    }

    // Request List
    public function RequestList()
    {
        $User = Auth::guard('userapi')->user();
        // dd($User);
        $DiamondRequest = DiamondRequest::with(['product','slot'])
        ->join('products', 'products.id','=','diamond_requests.product_id')
        ->where('diamond_requests.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_requests.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondRequest, 'message' => 'successfully.']], 200);
    }

    public function RequestStore(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $validator = Validator::make($request->all(),[
            'slot_id' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $msg = '';
        $count = 0;
        foreach(json_decode($request->product_id) as $product_id) {
            $Product = Product::where('confirm',0)->where('status',1)->where('id', $product_id)->first();
            if($Product) {
                $DiamondRequest1 = DiamondRequest::where('user_id', $User->id)->where('product_id', $product_id)->first();
                if($DiamondRequest1){
                    $msg .= $Product->stone_id.' already request list, ';
                } else {
                    $DiamondRequest = DiamondRequest::create([
                        'user_id' => $User->id,
                        'slot_id' => $request->slot_id,
                        'product_id' => $product_id,
                        'status' => 0
                    ]);

                    $data1 = [];
                    $data1['log_entrytype'] = 0;
                    $data1['user_id'] = $User->id;
                    $data1['LogName'] = $Product->stone_id;
                    $data1['LName'] = 'Create Diamond Request';
                    $data1['LogType'] = 13;
                    $data1['AdminType'] = 0;
                    $data1['log_id'] = $DiamondRequest->id;
                    $data1['product_id'] = $product_id;
                    event(new AdminLogStored($data1));
                    $count++;
                    // $details['email'] = 'ankit.borad93@gmail.com';
                    // Mail::to($request->email)->send(new RequestMail($details));
                }
            } else {
                $msg .= $Product->stone_id.' already confirm, ';
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Request successfully.','error_message' =>$msg,'count' => $count]], 200);
    }

    public function RequestDelete(Request $request)
    {
        foreach(json_decode($request->request_id) as $request_id) {
            $DiamondRequest = DiamondRequest::find($request_id);
            if($DiamondRequest) {
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $DiamondRequest->product->stone_id;
                $data['LName'] = 'Delete Diamond Request';
                $data['LogType'] = 19;
                $data['AdminType'] = 0;
                $data['log_id'] = $request_id;
                $data['product_id'] = $DiamondRequest->product_id;
                event(new AdminLogStored($data));
                $DiamondRequest->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Request deleted successfully.']], 200);
    }

    // Add to cart List
    public function CartList()
    {
        $User = Auth::guard('userapi')->user();
        $DiamondCart = DiamondCart::with(['product'])->where('user_id', $User->id)
        ->join('products', 'products.id','=','diamond_carts.product_id')
        ->where('diamond_carts.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_carts.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondCart, 'message' => 'successfully.']], 200);
    }

    public function CartStore(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $validator = Validator::make($request->all(),[
            'product_id' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $msg = '';
        $count = 0;
        foreach(json_decode($request->product_id) as $product_id) {
            $Product = Product::where('confirm',0)->where('status',1)->where('id', $product_id)->first();
            $Productdata = Product::find($product_id);
            if($Product) {
                $DiamondCart1 = DiamondCart::where('user_id', $User->id)->where('product_id', $product_id)->first();
                if($DiamondCart1){
                    $msg .= 'Stone Id : '.$Product->stone_id.' already Cart list, ';
                } else {
                    $DiamondCart = DiamondCart::create([
                        'user_id' => $User->id,
                        'product_id' => $product_id,
                        'status' => 1
                    ]);
                    $data1 = [];
                    $data1['log_entrytype'] = 0;
                    $data1['user_id'] = $User->id;
                    $data1['LogName'] = $Product->stone_id;
                    $data1['LName'] = 'Create Diamond Cart';
                    $data1['LogType'] = 15;
                    $data1['AdminType'] = 0;
                    $data1['log_id'] = $DiamondCart->id;
                    $data1['product_id'] = $product_id;
                    event(new AdminLogStored($data1));
                    $count++;
                }
            } else {
                $msg .= 'Stone Id : '.$Productdata->stone_id.' already confirm, ';
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Cart successfully.','error_message' =>$msg,'count' => $count]], 200);
    }

    public function CartDelete(Request $request)
    {
        foreach(json_decode($request->cart_id) as $cart_id) {
            $DiamondCart = DiamondCart::find($cart_id);
            if($DiamondCart) {
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $DiamondCart->product->stone_id;
                $data['LName'] = 'Delete Diamond Cart';
                $data['LogType'] = 15;
                $data['AdminType'] = 0;
                $data['log_id'] = $cart_id;
                $data['product_id'] = $DiamondCart->product_id;
                event(new AdminLogStored($data));
                $DiamondCart->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Cart deleted successfully.']], 200);
    }

    // Add to Hold List
    public function HoldList()
    {
        $User = Auth::guard('userapi')->user();
        $DiamondHold = DiamondHold::with(['product'])
        ->join('products', 'products.id','=','diamond_holds.product_id')
        ->where('diamond_holds.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_holds.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondHold, 'message' => 'successfully.']], 200);
    }

    public function HoldStore(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $validator = Validator::make($request->all(),[
            'product_id' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $msg = '';
        $count = 0;
        foreach(json_decode($request->product_id) as $product_id) {
            $Product = Product::where('hold',0)->where('confirm',0)->where('status',1)->where('id', $product_id)->first();
            $Productdata = Product::find($product_id);
            if($Product) {
                $DiamondHold1 = DiamondHold::where('user_id', $User->id)->where('product_id', $product_id)->first();
                if($DiamondHold1){
                    $msg .= 'Stone Id : '.$Product->stone_id.' already Hold list, ';
                } else {
                    $DiamondHold = DiamondHold::create([
                        'user_id' => $User->id,
                        'product_id' => $product_id,
                        'status' => 1
                    ]);
                    Product::find($product_id)->update(['hold' =>1]);
                    $data1 = [];
                    $data1['log_entrytype'] = 0;
                    $data1['user_id'] = $User->id;
                    $data1['LogName'] = $Product->stone_id;
                    $data1['LName'] = 'Create Diamond Hold';
                    $data1['LogType'] = 14;
                    $data1['AdminType'] = 0;
                    $data1['log_id'] = $DiamondHold->id;
                    $data1['product_id'] = $product_id;
                    event(new AdminLogStored($data1));
                    $Product->update(['hold' => 1]);
                    $count++;
                    // $details['email'] = 'ankit.borad93@gmail.com';
                    // Mail::to($request->email)->send(new HoldMail($details));
                }
            } else {
                $msg .= 'Stone Id : '.$Productdata->stone_id.' already confirm, ';
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Hold successfully.','error_message' =>$msg,'count' => $count]], 200);
    }

    public function HoldDelete(Request $request)
    {
        foreach(json_decode($request->hold_id) as $hold_id) {
            $DiamondHold = DiamondHold::find($hold_id);
            if($DiamondHold) {
                // if($DiamondHold->status == 0) {
                    Product::find($DiamondHold->product_id)->update(['hold' => 0]);
                // }
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $DiamondHold->product->stone_id ?? null;
                $data['LName'] = 'Delete Diamond Hold';
                $data['LogType'] = 14;
                $data['AdminType'] = 0;
                $data['log_id'] = $hold_id;
                $data['product_id'] = $DiamondHold->product_id;
                event(new AdminLogStored($data));
                $DiamondHold->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Hold deleted successfully.']], 200);
    }

    // Add to Confirm List
    public function ConfirmList()
    {
        $User = Auth::guard('userapi')->user();
        $DiamondConfirm = DiamondConfirm::with(['product'])
        ->join('products', 'products.id','=','diamond_confirms.product_id')
        ->where('diamond_confirms.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_confirms.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondConfirm, 'message' => 'successfully.']], 200);
    }

    public function ConfirmStore(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $validator = Validator::make($request->all(),[
            'product_id' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $msg = '';
        $count = 0;
        foreach(json_decode($request->product_id) as $product_id) {
            $Product = Product::where('hold',0)->where('confirm',0)->where('status',1)->where('id', $product_id)->first();
            $Productdata = Product::find($product_id);
            if($Product) {
                $DiamondConfirm1 = DiamondConfirm::where('user_id', $User->id)->where('product_id', $product_id)->first();
                if($DiamondConfirm1){
                    $msg .= 'Stone Id : '.$Product->stone_id.' already Confirm list, ';
                } else {
                    $DiamondConfirm = DiamondConfirm::create([
                        'user_id' => $User->id,
                        'product_id' => $product_id,
                        'status' => 1,
                        'reason' => $request->reason

                    ]);
                    Product::find($product_id)->update(['confirm' =>1]);
                    $data1 = [];
                    $data1['log_entrytype'] = 0;
                    $data1['user_id'] = $User->id;
                    $data1['LogName'] = $Product->stone_id ?? null;
                    $data1['LName'] = 'Create Diamond Confirm';
                    $data1['LogType'] = 16;
                    $data1['AdminType'] = 0;
                    $data1['log_id'] = $DiamondConfirm->id;
                    $data1['product_id'] = $product_id;
                    event(new AdminLogStored($data1));
                    $Product->update(['confirm' => 1]);
                    $count++;
                    // $details['email'] = 'ankit.borad93@gmail.com';
                    // Mail::to($request->email)->send(new ConfirmMail($details));
                }
            } else {
                $msg .= 'Stone Id : '.$Productdata->stone_id.' already confirm, ';
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Confirm successfully.','error_message' =>$msg,'count' => $count]], 200);
    }

    public function ConfirmDelete(Request $request)
    {
        foreach(json_decode($request->confirm_id) as $confirm_id) {
            $DiamondConfirm = DiamondConfirm::find($confirm_id);
            if($DiamondConfirm) {
                // if($DiamondConfirm->status == 0) {
                    Product::find($DiamondConfirm->product_id)->update(['confirm' => 0]);
                // }
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $DiamondConfirm->product->stone_id;
                $data['LName'] = 'Delete Diamond Confirm';
                $data['LogType'] = 16;
                $data['AdminType'] = 0;
                $data['log_id'] = $confirm_id;
                $data['product_id'] = $DiamondConfirm->product_id;
                event(new AdminLogStored($data));
                $DiamondConfirm->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Confirm deleted successfully.']], 200);
    }

    // Add to Inquiry List
    public function InquiryList()
    {
        $User = Auth::guard('userapi')->user();
        $DiamondInquiry = DiamondInquiry::with(['product'])
        ->join('products', 'products.id','=','diamond_inquiries.product_id')
        ->where('diamond_inquiries.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_inquiries.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondInquiry, 'message' => 'successfully.']], 200);
    }

    public function InquiryStore(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $validator = Validator::make($request->all(),[
            'product_id' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $msg = '';
        foreach(json_decode($request->product_id) as $product_id) {
            $Product = Product::where('confirm',0)->where('status',1)->where('id', $product_id)->first();
            $Productdata = Product::find($product_id);
            if($Product) {
                $DiamondInquiry1 = DiamondInquiry::where('user_id', $User->id)->where('product_id', $product_id)->first();
                if($DiamondInquiry1){
                    $msg .= 'Stone Id : '.$Product->stone_id.' already Inquiry list, ';
                } else {
                    $DiamondInquiry = DiamondInquiry::create([
                        'user_id' => $User->id,
                        'product_id' => $product_id,
                        'status' => 1,
                        'comment' => $request->comment
                    ]);

                    $data1 = [];
                    $data1['log_entrytype'] = 0;
                    $data1['user_id'] = $User->id;
                    $data1['LogName'] = $Product->stone_id;
                    $data1['LName'] = 'Create Diamond inquiry';
                    $data1['LogType'] = 17;
                    $data1['AdminType'] = 0;
                    $data1['log_id'] = $DiamondInquiry->id;
                    $data1['product_id'] = $product_id;
                    event(new AdminLogStored($data1));
                }
            } else {
                $msg .= 'Stone Id : '. $Productdata->stone_id.' already confirm or hold, ';
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Inquiry successfully.','error_message' =>$msg]], 200);
    }

    public function InquiryDelete(Request $request)
    {
        foreach(json_decode($request->inquiry_id) as $inquiry_id) {
            $DiamondInquiry = DiamondInquiry::find($inquiry_id);
            if($DiamondInquiry) {
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $DiamondInquiry->product->stone_id;
                $data['LName'] = 'Delete Diamond Inquiry';
                $data['LogType'] = 17;
                $data['AdminType'] = 0;
                $data['log_id'] = $inquiry_id;
                $data['product_id'] = $DiamondInquiry->product_id;
                event(new AdminLogStored($data));
                $DiamondInquiry->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Inquiry deleted successfully.']], 200);
    }

    public function exportExcel(Request $request)
    {
        $name = 'DD_WEB_'.date('dmY_His').'.xlsx';
        $details['stone_id'] = json_decode($request->stone_id);
        $details['dt'] = $request->dt ?? 1;
        return Excel::download(new DiamondExport($details), $name);
    }

    public function exportPdf(Request $request)
    {
        if($request->has("selectAll") && $request->selectAll == 1) {

        } else {
            $stone = implode("','",json_decode($request->stone_id));
            $name = 'DD_WEB_'.date('dmY_His').'.pdf';
            // $Product = Product::whereIn('stone_id', json_decode($request->stone_id))->get();
            $Product = DB::select("select
            p.stone_id, p.cert_type, p.cert_url, p.cert_no, p.carat, p.discount, p.rate, p.amount, p.measurement, p.ratio, p.image, p.video, p.rapo_amount,
            -- s.shape_name, c.color_name, cl.clarity_name, cu.cut_name, po.polish_name, cy.symmetry_name, fl.fluorescence_name,
            ifnull((SELECT name from shapes s where s.id=p.shape_id),'') as shape_name,
            ifnull((SELECT name from colors c where c.id=p.color_id),'') as color_name,
            ifnull((SELECT name from clarities cl where cl.id=p.clarity_id),'') as clarity_name,
            ifnull((SELECT name from finishes cu where cu.id=p.cut_id),'') as cut_name,
            ifnull((SELECT name from finishes po where po.id=p.polish_id),'') as polish_name,
            ifnull((SELECT name from finishes fi where fi.id=p.symmetry_id),'') as symmetry_name,
            ifnull((SELECT name from fluorescences f where f.id=p.fluorescence_id),'') as fluorescence_name
            from
            products p
            where p.stone_id IN ('$stone')");
        }

        $data = [
            'title' => 'Delight Diamond',
            'date' => date('d-m-Y'),
            'Products' => $Product,
            'dt' => $request->dt ?? 1
        ];

        $pdf = PDF::loadView('export', $data)->setPaper('a4', 'landscape');

        return $pdf->download($name);
    }

    public function emailExcel(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $name = 'DD_WEB_'.date('dmY_His').'.xlsx';
        $data["email"] = $User->email;
        $data["title"] = 'Delight Diamond';
        $data["body"] = "This is system generated email with attachment";
        $details['stone_id'] = json_decode($request->stone_id);
        $details['dt'] = $request->dt ?? 1;
        $file = Excel::raw(new DiamondExport($details), BaseExcel::XLSX);

        Mail::send('emails.mail', $data, function($message)use($data, $file, $name) {
            $message->to($data["email"])
                    ->subject($data["title"]);
            $message->attachData($file, $name);
        });
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Email send successfully.']], 200);
    }

    public function details($id)
    {
        $Diamond = Product::find($id);
        return response()->json(['success' => true, 'data' => ['result' => $Diamond, 'message' => 'successfully.']], 200);
    }
}
