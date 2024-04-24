<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Exports\HistoryExport;
use App\Http\Controllers\Controller;
use App\Models\Clarity;
use App\Models\Color;
use App\Models\FancyColor;
use App\Models\Finish;
use App\Models\Product;
use App\Models\Shape;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportProduct;
use App\Models\AdminLog;
use App\Models\DiamondCart;
use App\Models\DiamondConfirm;
use App\Models\DiamondHold;
use App\Models\DiamondInquiry;
use App\Models\DiamondRequest;
use App\Models\Fluorescence;
use App\Models\TempProduct;
use App\Models\UploadHistory;
use Illuminate\Support\Facades\DB;

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
        $Product = Product::whereNull('vendor_id')->get();
        // dd($Product);
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

        $uuid = Str::uuid();
        // dd($uuid);
        $data['id'] = $uuid;
        $data['availability'] = $request->availability ?? null;
        $data['stone_id'] = $request->stone_id;
        $data['cert_no'] = $request->cert_no ?? null;
        $data['cert_type'] = $request->cert_type ?? null;
        $data['cert_url'] = $request->cert_url ?? null;
        $data['image'] = $request->image ?? null;
        $data['video'] = $request->video ?? null;
        $data['diamond_type'] = $request->diamond_type;
        $data['carat'] = $request->carat ?? 0;
        $data['shape_id'] = $request->shape_id ?? null;
        $data['color_id'] = $request->color_id ?? null;

        $data['clarity_id'] = $request->clarity_id ?? null;
        $data['cut_id'] = $request->cut_id ?? null;
        $data['polish_id'] = $request->polish_id ?? null;
        $data['symmetry_id'] = $request->symmetry_id ?? null;
        $data['fluorescence_id'] = $request->fluorescence_id ?? null;
        $data['rapo_rate'] = $request->rapo_rate ?? 0;
        $data['rapo_amount'] = ($request->carat * $request->rapo_rate);
        $data['discount'] = $request->discount ?? 0;
        $data['rate'] = $request->rate ?? 0;
        $data['amount'] = $request->amount ?? 0;
        $data['table_per'] = $request->table_per ?? 0;
        $data['depth_per'] = $request->depth_per ?? 0;
        $data['length'] = $request->length ?? 0;
        $data['width'] = $request->width ?? 0;
        $data['height'] = $request->height ?? 0;
        $data['ratio'] = $request->ratio ?? 0;
        $data['country'] = $request->country ?? null;
        $data['colors_id'] = $request->colors_id ?? null;
        $data['overtone_id'] = $request->overtone_id ?? null;
        $data['intensity_id'] = $request->intensity_id ?? null;
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
        }
        if($request->diamond_type == 2) {
            $data['measurement'] = $request->measurement;
            $data['bgm_id'] = $request->bgm_id;
            $data['fluorescence_color_id'] = $request->fluorescence_color_id ?? null;
            $data['pair'] = $request->pair == 'no' || $request->pair == 0 || $request->pair == '' ? null : ($request->pair ?? null);
            $data['h_a'] = $request->h_a ?? null;
            $data['city'] = $request->city ?? null;
            $data['growth_type'] = $request->growth_type ?? null;
            $data['treatment'] = $request->treatment ?? null;
        }

        $Product = Product::create($data);
        $Product = Product::find($uuid);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->stone_id;
        $data1['LName'] = 'Create Diamond';
        $data1['LogType'] = 21;
        $data1['AdminType'] = 1;
        $data1['log_id'] = 0;
        $data1['product_id'] = $Product->id;

        event(new AdminLogStored($data1));
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

            $data['availability'] = $request->availability ?? null;
            $data['stone_id'] = $request->stone_id;
            $data['cert_no'] = $request->cert_no ?? null;
            $data['cert_type'] = $request->cert_type ?? null;
            $data['cert_url'] = $request->cert_url ?? null;
            $data['image'] = $request->image ?? null;
            $data['video'] = $request->video ?? null;
            $data['diamond_type'] = $request->diamond_type;
            $data['carat'] = $request->carat ?? 0;
            $data['shape_id'] = $request->shape_id ?? null;
            $data['color_id'] = $request->color_id ?? null;

            $data['clarity_id'] = $request->clarity_id ?? null;
            $data['cut_id'] = $request->cut_id ?? null;
            $data['polish_id'] = $request->polish_id ?? null;
            $data['symmetry_id'] = $request->symmetry_id ?? null;
            $data['fluorescence_id'] = $request->fluorescence_id ?? null;
            $data['rapo_rate'] = $request->rapo_rate ?? 0;
            $data['rapo_amount'] = ($request->carat * $request->rapo_rate);
            $data['discount'] = $request->discount ?? 0;
            $data['rate'] = $request->rate ?? 0;
            $data['amount'] = $request->amount ?? 0;
            $data['table_per'] = $request->table_per ?? 0;
            $data['depth_per'] = $request->depth_per ?? 0;
            $data['length'] = $request->length ?? 0;
            $data['width'] = $request->width ?? 0;
            $data['height'] = $request->height ?? 0;
            $data['ratio'] = $request->ratio ?? 0;
            $data['country'] = $request->country ?? null;
            $data['colors_id'] = $request->colors_id ?? null;
            $data['overtone_id'] = $request->overtone_id ?? null;
            $data['intensity_id'] = $request->intensity_id ?? null;
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

            $data['name'] = $request->name;
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;

            $data['LogName'] = $Product->stone_id;
            $data['LName'] = 'Diamond';
            $data['LogType'] = 21;
            $data['AdminType'] = 1;

            $data['log_id'] = 0;
            $data['product_id'] = $id;
            event(new AdminLogStored($data));
            $Product = $Product->update($data);
            return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'successfully.']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond not found']], 500);
    }

    public function delete($id)
    {
        $DiamondConfirm = DiamondConfirm::where('product_id',$id)->first();
        if($DiamondConfirm){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Diamond belongs to Diamond Confirm in the system and cannot be deleted.']], 500);
        } else {
            $Product = Product::find($id);
            if($Product){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Product->stone_id;
                $data1['LName'] = 'Delete Diamond';
                $data1['LogType'] = 21;
                $data1['AdminType'] = 1;
                $data1['log_id'] = 0;
                $data1['product_id'] = $id;
                event(new AdminLogStored($data1));
                $Product->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Product = Product::find($id);
        if($Product){
            $data1 = $request->all();
            $data1['log_entrytype'] = 1;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Product->stone_id;
            $data1['LName'] = 'Diamond';
            $data1['LogType'] = 21;
            $data1['AdminType'] = 1;
            $data1['log_id'] = 0;
            $data1['product_id'] = $id;
            event(new AdminLogStored($data1));
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
        $uuid = Str::uuid();
        $details['uuid'] = $uuid;
        $details['diamond_type'] = $request->diamond_type;
        $details['vendor_id'] = null;
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
                'uuid' => $uuid
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $product, 'uuid' => $uuid, 'message' => 'successfully.']], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => ['message' => 'Product import errro', 'error_message' => $th]], 422);
        }
    }

    public function importstore(Request $request, $uuid)
    {
        if($request->status == 1){
            try {
                DB::select("CALL ProductUpload('".$uuid."')");
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'successfully.']], 200);
            } catch (\Throwable $th) {
                return response()->json(['success' => false, 'error' => ['message' => 'Product import errro', 'error_message' => $th]], 200);
            } finally {
                TempProduct::where('uuid', $uuid)->delete();
            }
        }
        if($request->status == 2){
            TempProduct::where('uuid', $uuid)->delete();
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'successfully.']], 200);
        }
    }

    public function updateImportData(Request $request, $uuid)
    {
        $data = json_decode($request->data);
        foreach($data as $key => $val) {
            $dd['stone_id'] = $val->stone_id;
            $dd['cert_no'] = $val->cert_no;
            $dd['cert_type'] = $val->cert_type ?? 0;
            $dd['cert_url'] = $val->cert_url ?? null;
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
        return response()->json(['success' => true, 'data' => ['result' => $product, 'uuid' => $uuid, 'message' => 'successfully.']], 200);
    }

    public function invaliddiamond(Request $request)
    {
        if($request->diamond_type == 1) {
            $product = DB::select("CALL AllProductImportErrorN(0, 1)");
        }
        if($request->diamond_type == 2) {
            $product = DB::select("CALL AllProductImportErrorL(0, 1)");
        }
        return response()->json(['success' => true, 'data' => ['result' => $product, 'message' => 'successfully.']], 200);
    }

    public function history()
    {
        $History = UploadHistory::whereNull('vendor_id')->get();
        return response()->json(['success' => true, 'data' => ['result' => $History, 'message' => 'successfully.']], 200);
    }

    public function exceldownload($id)
    {
        $History = UploadHistory::find($id);
        $tempproduct = TempProduct::where('uuid', $History->uuid)->first();
        $name = $History->file_name;
        $details['id'] = $History->uuid;
        $details['dt'] = $tempproduct->diamond_type;
        return Excel::download(new HistoryExport($details), $name);
    }

    public function resetdiamond($type)
    {
        // $cart = DiamondCart::pluck('product_id')->toArray();
        // $request = DiamondRequest::pluck('product_id')->toArray();
        // $inquiry = DiamondInquiry::pluck('product_id')->toArray();
        // $hold = DiamondHold::pluck('product_id')->toArray();
        // $confirm = DiamondConfirm::pluck('product_id')->toArray();
        // $diamond = array_unique(array_merge($cart, $request, $inquiry, $hold, $confirm));
        // AdminLog::whereNotIn('id', $diamond)->forceDelete();
        // DiamondCart::onlyTrashed()->forceDelete();
        // DiamondRequest::onlyTrashed()->forceDelete();
        // DiamondInquiry::onlyTrashed()->forceDelete();
        // DiamondHold::onlyTrashed()->forceDelete();
        // DiamondConfirm::onlyTrashed()->forceDelete();
        // Product::whereNull('vendor_id')->whereNotIn('id', $diamond)->forceDelete();
        $Products = Product::where('diamond_type', $type)->whereNull('vendor_id')->select('id')->get();
        foreach($Products as $key => $product) {
            // $dd = DiamondConfirm::where('product_id', $product->id)->withTrashed();
            // dd($dd);
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
