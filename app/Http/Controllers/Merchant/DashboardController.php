<?php

namespace App\Http\Controllers\Merchant;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\MerchantMail;
use App\Mail\RegisterMail;
use App\Models\Country;
use App\Models\DiamondConfirm;
use App\Models\DiamondHold;
use App\Models\Merchant;
use App\Models\Otp;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'email','unique:merchants,email','unique:users,email'],
            'username' => ['required','unique:merchants,username','unique:users,username']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 422);
        }

        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['company'] = $request->company;
        $data['website'] = $request->website;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['mobile'] = $request->mobile;
        $data['other_phone'] = $request->other_phone;
        $data['about'] = $request->about;
        $data['job_title'] = $request->job_title;
        $data['business_type'] = json_encode($request->business_type);
        $data['buying_group'] = json_encode($request->buying_group);
        $data['group_title'] = $request->group_title ?? null;
        $data['address'] = $request->address;
        $data['city'] = $request->city;
        $data['state'] = $request->state;
        $data['pincode'] = $request->pincode;
        $data['country_id'] = $request->country_id;

        if($request->has('document')){
            $file = $request->file('document');
            $filename = str_replace(' ', '', $request->username.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/document', $filename);
            $data['document'] = $filename;
        }
        $Merchants = Merchant::create($data);
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make('kings@123'),
            'fullname' => $request->first_name.' '.$request->last_name,
            'country_id' => $request->country_id,
            'company' => $request->company,
            'mobile' => $request->mobile,
            'merchant_id' => $Merchants->id,
            'user_type' => 2
        ]);
        $hashid = Crypt::encryptString($Merchants->id);
        $details['subject'] = 'Confirm your email address to get started on Delight Diamond';
        $details['email'] = $request->email;
        $details['url'] = 'https://delightdiamonds.com/merchant/activation/' .$hashid;
        Mail::to($request->email)->send(new RegisterMail($details));
        return response()->json(['success' => true, 'data' => ['result' => $Merchants, 'message' => 'successfully.']], 200);
    }

    public function activation($id)
    {
        $hashid1 = Crypt::decryptString($id);
        $Merchant = Merchant::find($hashid1);
        if($Merchant) {
            $hashid = Crypt::encryptString($Merchant->id);
            Merchant::find($hashid1)->update([
                'verified_at' => date('Y-m-d H:i:s'),
                'token' => $hashid
            ]);
            $details['subject'] = 'Delight Diamond Activation key for merchant panel';
            $details['email'] = $Merchant->email;
            $details['key'] = $hashid;
            Mail::to($Merchant->email)->send(new MerchantMail($details));
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Email verified successfully, your activation key send to your email']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid token please try again']], 401);
    }

    public function country()
    {
        $data['country'] = Country::all();
        $data['BusinessType'] = Merchant::$BusinessType;
        $data['ByuingGroup'] = Merchant::$ByuingGroup;
        return response()->json(['success' => true, 'data' => ['result' => $data, 'message' => 'success']], 200);
    }

    public function dashboard(Request $request)
    {
        $header = $request->header('token');
        $Merchant = Merchant::where('token', $header)->first();
        $User = User::where('merchant_id', $Merchant->id)->where('user_type', 2)->first();
        $result['Hold'] = DiamondHold::where('user_id', $User->id)->count();
        $result['confirm'] = DiamondConfirm ::where('user_id', $User->id)->count();
        return response()->json(['success' => true, 'data' => ['result' => $result, 'message' => 'success']], 200);
    }

    public function list()
    {
        $Product = Product::where('status', 1)->where('confirm',0);
        $Product = $Product->get();
        return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'successfully.']], 200);
    }

    // Add to Hold List
    public function HoldList(Request $request)
    {
        $header = $request->header('token');
        $Merchant = Merchant::where('token', $header)->first();
        $User = User::where('merchant_id', $Merchant->id)->where('user_type', 2)->first();
        $DiamondHold = DiamondHold::with(['product'])
        ->join('products', 'products.id','=','diamond_holds.product_id')
        ->where('diamond_holds.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_holds.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondHold, 'message' => 'successfully.']], 200);
    }

    public function HoldStore(Request $request)
    {
        $header = $request->header('token');
        $Merchant = Merchant::where('token', $header)->first();
        $User = User::where('merchant_id', $Merchant->id)->where('user_type', 2)->first();
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
        $header = $request->header('token');
        $Merchant = Merchant::where('token', $header)->first();
        $User = User::where('merchant_id', $Merchant->id)->where('user_type', 2)->first();
        foreach(json_decode($request->hold_id) as $hold_id) {
            $DiamondHold = DiamondHold::find($hold_id);
            if($DiamondHold) {
                Product::find($DiamondHold->product_id)->update(['hold' => 0]);
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = $User->id;
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
    public function ConfirmList(Request $request)
    {
        $header = $request->header('token');
        $Merchant = Merchant::where('token', $header)->first();
        $User = User::where('merchant_id', $Merchant->id)->where('user_type', 2)->first();
        $DiamondConfirm = DiamondConfirm::with(['product'])
        ->join('products', 'products.id','=','diamond_confirms.product_id')
        ->where('diamond_confirms.user_id', $User->id)->where('products.diamond_type', $User->diamond_type)
        ->select('diamond_confirms.*')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondConfirm, 'message' => 'successfully.']], 200);
    }

    public function ConfirmStore(Request $request)
    {
        $header = $request->header('token');
        $Merchant = Merchant::where('token', $header)->first();
        $User = User::where('merchant_id', $Merchant->id)->where('user_type', 2)->first();
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



}
