<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Imports\ImportProduct;
use App\Models\AdminLog;
use App\Models\Clarity;
use App\Models\Color;
use App\Models\DiamondCart;
use App\Models\DiamondConfirm;
use App\Models\DiamondHold;
use App\Models\DiamondInquiry;
use App\Models\DiamondRequest;
use App\Models\FancyColor;
use App\Models\Finish;
use App\Models\Fluorescence;
use App\Models\Product;
use App\Models\Shape;
use App\Models\Size;
use App\Models\TempProduct;
use App\Models\UploadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    public function master()
    {
        $result['Shape'] = Shape::all();
        $result['Color'] = Color::all();
        $result['Clarity'] = Clarity::all();
        $result['Size'] = Size::all();
        $result['FancyColor'] = FancyColor::all();
        $result['Finish'] = Finish::all();
        $result['Fluorescence'] = Fluorescence::all();
        $result['Lab'] = Product::$Lab;
        $result['DiamondType'] = Product::$DiamondType;
        $result['Type'] = Product::$Type;
        return response()->json(['success' => true, 'data' => ['result' => $result, 'message' => 'successfully.']], 200);
    }

    public function index()
    {
        $User = Auth::guard('vendorapi')->user();
        $Product = Product::where('vendor_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'stone_id' => ['required','unique:products,stone_id'],
            'cert_no' => ['required','unique:products,cert_no']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        $User = Auth::guard('vendorapi')->user();
        $uuid = Str::uuid();
        $data['id'] = $uuid;
        $data['availability'] = $request->availability;
        $data['vendor_id'] = $User->id;
        $data['stone_id'] = $request->stone_id;
        $data['cert_no'] = $request->cert_no;
        $data['cert_type'] = $request->cert_type;
        $data['cert_url'] = $request->cert_url;
        $data['image'] = $request->image;
        $data['video'] = $request->video;
        $data['diamond_type'] = $request->diamond_type;
        $data['carat'] = $request->carat;
        $data['shape_id'] = $request->shape_id;
        $data['color_id'] = $request->color_id;

        $data['clarity_id'] = $request->clarity_id;
        $data['cut_id'] = $request->cut_id;
        $data['polish_id'] = $request->polish_id;
        $data['symmetry_id'] = $request->symmetry_id;
        $data['fluorescence_id'] = $request->fluorescence_id;
        $data['rapo_rate'] = $request->rapo_rate;
        $data['rapo_amount'] = ($request->carat * $request->rapo_rate);
        $data['discount'] = $request->discount;
        $data['rate'] = $request->rate;
        $data['amount'] = $request->amount;
        $data['table_per'] = $request->table_per;
        $data['depth_per'] = $request->depth_per;
        $data['length'] = $request->length;
        $data['width'] = $request->width;
        $data['height'] = $request->height;
        $data['ratio'] = $request->ratio;
        $data['country'] = $request->country;
        $data['colors_id'] = $request->colors_id;
        $data['overtone_id'] = $request->overtone_id;
        $data['intensity_id'] = $request->intensity_id;
        $data['eye_clean'] = $request->eye_clean ?? 'No';
        if($request->diamond_type == 1) {
            $data['milky'] = $request->milky ?? 0;
            $data['shade'] = $request->shade ?? 0;
            $data['crown_angle'] = $request->crown_angle ?? 0;
            $data['crown_height'] = $request->crown_height ?? 0;
            $data['pavilion_angle'] = $request->pavilion_angle ?? 0;
            $data['pavilion_height'] = $request->pavilion_height ?? 0;
            $data['white_table'] = $request->white_table ?? null;
            $data['white_side'] = $request->white_side ?? null;
            $data['table_black'] = $request->table_black ?? null;
            $data['side_black'] = $request->side_black ?? null;
            $data['table_open'] = $request->table_open ?? null;
            $data['pavilion_open'] = $request->pavilion_open ?? null;
            $data['crown_open'] = $request->crown_open ?? null;
            $data['girdle'] = $request->girdle ?? 0;
            $data['girdle_desc'] = $request->girdle_desc ?? null;
            $data['culet'] = $request->culet ?? null;
            $data['key_to_symbols'] = $request->key_to_symbols ?? null;
            $data['inscription'] = $request->inscription ?? null;
            $data['comment'] = $request->comment ?? null;
            $data['diamond_type'] = 1;
        }
        if($request->diamond_type == 2) {
            $data['measurement'] = $request->measurement;
            $data['bgm_id'] = $request->bgm_id;
            $data['fluorescence_color_id'] = $request->fluorescence_color_id ?? null;
            $data['pair'] = $request->pair ?? null;
            $data['h_a'] = $request->h_a ?? null;
            $data['city'] = $request->city ?? null;
            $data['growth_type'] = $request->growth_type ?? null;
            $data['treatment'] = $request->treatment ?? null;
        }

        $Product = Product::create($data);
        $Product = Product::find($uuid);
        // $data1 = [];
        // $data1['log_entrytype'] = 0;
        // $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        // $data1['LogName'] = $request->stone_id;
        // $data1['LName'] = 'Create Diamond';
        // $data1['LogType'] = 21;
        // $data1['AdminType'] = 1;
        // $data1['log_id'] = 0;
        // $data1['product_id'] = $Product->id;

        // event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'successfully.']], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'stone_id' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $Product = Product::find($id);
        if($Product) {

            $data['availability'] = $request->availability;
            $data['stone_id'] = $request->stone_id;
            $data['cert_no'] = $request->cert_no;
            $data['cert_type'] = $request->cert_type;
            $data['cert_url'] = $request->cert_url;
            $data['image'] = $request->image;
            $data['video'] = $request->video;
            $data['diamond_type'] = $request->diamond_type;
            $data['carat'] = $request->carat;
            $data['shape_id'] = $request->shape_id;
            $data['color_id'] = $request->color_id;

            $data['clarity_id'] = $request->clarity_id;
            $data['cut_id'] = $request->cut_id;
            $data['polish_id'] = $request->polish_id;
            $data['symmetry_id'] = $request->symmetry_id;
            $data['fluorescence_id'] = $request->fluorescence_id;
            $data['rapo_rate'] = $request->rapo_rate;
            $data['rapo_amount'] = ($request->carat * $request->rapo_rate);
            $data['discount'] = $request->discount;
            $data['rate'] = $request->rate;
            $data['amount'] = $request->amount;
            $data['table_per'] = $request->table_per;
            $data['depth_per'] = $request->depth_per;
            $data['length'] = $request->length;
            $data['width'] = $request->width;
            $data['height'] = $request->height;
            $data['ratio'] = $request->ratio;
            $data['country'] = $request->country;
            $data['colors_id'] = $request->colors_id;
            $data['overtone_id'] = $request->overtone_id;
            $data['intensity_id'] = $request->intensity_id;
            $data['eye_clean'] = $request->eye_clean ?? 'No';
            if($request->diamond_type == 1) {
                $data['milky'] = $request->milky ?? 0;
                $data['shade'] = $request->shade ?? 0;
                $data['crown_angle'] = $request->crown_angle ?? 0;
                $data['crown_height'] = $request->crown_height ?? 0;
                $data['pavilion_angle'] = $request->pavilion_angle ?? 0;
                $data['pavilion_height'] = $request->pavilion_height ?? 0;
                $data['white_table'] = $request->white_table ?? null;
                $data['white_side'] = $request->white_side ?? null;
                $data['table_black'] = $request->table_black ?? null;
                $data['side_black'] = $request->side_black ?? null;
                $data['table_open'] = $request->table_open ?? null;
                $data['pavilion_open'] = $request->pavilion_open ?? null;
                $data['crown_open'] = $request->crown_open ?? null;
                $data['girdle'] = $request->girdle ?? 0;
                $data['girdle_desc'] = $request->girdle_desc ?? null;
                $data['culet'] = $request->culet ?? null;
                $data['key_to_symbols'] = $request->key_to_symbols ?? null;
                $data['inscription'] = $request->inscription ?? null;
                $data['comment'] = $request->comment ?? null;
                $data['diamond_type'] = 1;
            }
            if($request->diamond_type == 2) {
                $data['measurement'] = $request->measurement;
                $data['bgm_id'] = $request->bgm_id;
                $data['fluorescence_color_id'] = $request->fluorescence_color_id ?? null;
                $data['pair'] = $request->pair ?? null;
                $data['h_a'] = $request->h_a ?? null;
                $data['city'] = $request->city ?? null;
                $data['growth_type'] = $request->growth_type ?? null;
                $data['treatment'] = $request->treatment ?? null;
            }

            // $data['name'] = $request->name;
            // $data['log_entrytype'] = 1;
            // $data['user_id'] = auth()->guard('adminapi')->user()->id;

            // $data['LogName'] = $Product->stone_id;
            // $data['LName'] = 'Diamond';
            // $data['LogType'] = 21;
            // $data['AdminType'] = 1;

            // $data['log_id'] = 0;
            // $data['product_id'] = $id;
            // event(new AdminLogStored($data));
            $Product = $Product->update($data);
            return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'successfully.']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond not found']], 500);
    }

    public function status(Request $request, $id)
    {
        $Product = Product::find($id);
        if($Product){
            // $data1 = $request->all();
            // $data1['log_entrytype'] = 1;
            // $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            // $data1['LogName'] = $Product->stone_id;
            // $data1['LName'] = 'Diamond';
            // $data1['LogType'] = 21;
            // $data1['AdminType'] = 1;
            // $data1['log_id'] = 0;
            // $data1['product_id'] = $id;
            // event(new AdminLogStored($data1));
            $Product->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond not found']], 500);
        }
    }

    public function import(Request $request)
    {
        $User = Auth::guard('vendorapi')->user();
        $uuid = Str::uuid();
        $details['uuid'] = $uuid;
        $details['diamond_type'] = $request->diamond_type;
        $details['vendor_id'] = $User->id;
        $upload_type = $request->upload_type ?? 2;
        if($upload_type == 1){
            $this->resetdiamond($request->diamond_type);
        }
        Excel::import(new ImportProduct($details), $request->file('excel')->store('files'));
        try {
            if($request->diamond_type == 1) {
                $product = DB::select("CALL CheckProductImportErrorN('".$uuid."')");
            }
            if($request->diamond_type == 2) {
                $product = DB::select("CALL CheckProductImportErrorL('".$uuid."')");
            }
            DB::select("CALL ProductUpload('".$uuid."')");
            $total = TempProduct::where('uuid', $uuid)->count();
            $invalid = TempProduct::where('uuid', $uuid)->where('import_type', 0)->count();
            UploadHistory::create([
                'file_name' => $request->file('excel')->getClientOriginalName(),
                'upload_type' => 1,
                'status' => 1,
                'total' => $total,
                'valid' => $total - $invalid,
                'invalid' => $invalid,
                'uuid' => $uuid,
                'vendor_id' => $User->id,
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $product, 'uuid' => $uuid, 'message' => 'successfully.']], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => ['message' => 'Product import errro', 'error_message' => $th]], 200);
        }
    }

    public function updateImportData(Request $request, $uuid)
    {
        $User = Auth::guard('vendorapi')->user();
        $data = json_decode($request->data);
        foreach($data as $key => $val) {
            // dd(isset($val->cert_no));
            $dd['stone_id'] = $val->stone_id;
            if(isset($val->cert_no)){
                $dd['cert_no'] = $val->cert_no;
                $dd['cert_type'] = $val->cert_type ?? 0;
                $dd['cert_url'] = $val->cert_url ?? null;
            }
            $dd['carat'] = $val->carat ?? 0;
            $dd['shape_id'] = $val->shape_id ?? null;
            $dd['color_id'] = $val->color_id ?? null;
            $dd['clarity_id'] = $val->clarity_id ?? null;
            $dd['polish_id'] = $val->polish_id ?? null;
            $dd['symmetry_id'] = $val->symmetry_id ?? null;
            $dd['fluorescence_id'] = $val->fluorescence_id ?? null;
            $dd['rapo_rate'] = $val->rapo_rate ?? 0;
            $dd['discount'] = $val->discount ?? 0;
            $dd['rate'] = $val->rate ?? 0;
            $dd['rate'] = $val->amount ?? 0;
            $dd['table_per'] = $val->table_per ?? 0;
            $dd['depth_per'] = $val->depth_per ?? 0;
            $dd['length'] = $val->length ?? 0;
            $dd['width'] = $val->width ?? 0;
            $dd['height'] = $val->height ?? 0;
            $dd['country'] = $val->country ?? null;
            // dd($dd);
            $TempProduct = TempProduct::find($val->id);
            $TempProduct->update($dd);
        }
        if($request->diamond_type == 1) {
            $product = DB::select("CALL CheckProductImportErrorN('".$uuid."')");
        }
        if($request->diamond_type == 2) {
            $product = DB::select("CALL CheckProductImportErrorL('".$uuid."')");
        }
        DB::select("CALL ProductUpload('".$uuid."')");
        $total = TempProduct::where('uuid', $uuid)->count();
        $invalid = TempProduct::where('uuid', $uuid)->where('import_type', 0)->count();
        UploadHistory::updateOrCreate([
            'uuid' => $uuid,
        ],
        [
            'upload_type' => 1,
            'status' => 1,
            'total' => $total,
            'valid' => $total - $invalid,
            'invalid' => $invalid,
            'uuid' => $uuid,
            'vendor_id' => $User->id,
        ]);
        return response()->json(['success' => true, 'data' => ['result' => $product, 'uuid' => $uuid, 'message' => 'successfully.']], 200);
    }

    public function resetdiamond($type)
    {
        $User = Auth::guard('vendorapi')->user();
        // $Products = Product::where('diamond_type', $type)->where('vendor_id', $User->id)->where('confirm', 0)->where('hold', 0)->select('id')->get();
        // foreach($Products as $key => $product) {
        //     AdminLog::where('product_id', $product->id)->forceDelete();
        //     DiamondCart::where('product_id', $product->id)->withTrashed()->forceDelete();
        //     DiamondRequest::where('product_id', $product->id)->withTrashed()->forceDelete();
        //     DiamondInquiry::where('product_id', $product->id)->withTrashed()->forceDelete();
        //     DiamondHold::where('product_id', $product->id)->withTrashed()->forceDelete();
        //     DiamondConfirm::where('product_id', $product->id)->withTrashed()->forceDelete();
        //     Product::where('id',$product->id)->withTrashed()->forceDelete();
        // }

        $Products = Product::where('diamond_type', $type)->where('vendor_id', $User->id)->select('id')->get();
        foreach($Products as $key => $product) {
            AdminLog::where('product_id', $product->id)->forceDelete();
            DiamondCart::where('product_id', $product->id)->withTrashed()->forceDelete();
            DiamondRequest::where('product_id', $product->id)->withTrashed()->forceDelete();
            DiamondInquiry::where('product_id', $product->id)->withTrashed()->forceDelete();
            DiamondHold::where('product_id', $product->id)->withTrashed()->forceDelete();
            DiamondConfirm::where('product_id', $product->id)->withTrashed()->forceDelete();
            Product::where('id',$product->id)->withTrashed()->forceDelete();
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'successfully.']], 200);
    }
}
