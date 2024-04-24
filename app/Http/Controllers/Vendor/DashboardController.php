<?php

namespace App\Http\Controllers\Vendor;

use App\Exports\HistoryExport;
use App\Http\Controllers\Controller;
use App\Imports\ImportProduct;
use App\Models\Product;
use App\Models\TempProduct;
use App\Models\UploadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $User = Auth::guard('vendorapi')->user();
        $result['hold'] = Product::where('status', 1)->where("hold",1)->where("confirm", 0)->where("vendor_id", $User->id)->count();
        $result['confirm'] = Product::where('status', 1)->where("confirm", 1)->where("vendor_id", $User->id)->count();
        // $result['sales'] = 0;
        $result['active_stone'] = Product::where('status', 1)->where("hold", 0)->where("confirm", 0)->where("vendor_id", $User->id)->count();
        $result['invalid_stone'] = TempProduct::where("import_type", 0)->where("vendor_id", $User->id)->count();
        return response()->json(['success' => true, 'data' => ['result' => $result, 'message' => 'successfully.']], 200);
    }

    public function history()
    {
        $User = Auth::guard('vendorapi')->user();
        $History = UploadHistory::where('vendor_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $History, 'message' => 'successfully.']], 200);
    }

    public function exceldownload($id)
    {
        $History = UploadHistory::find($id);
        $tempproduct = TempProduct::where('uuid', $History->uuid)->first();

        $name = $History->file_name;
        $details['id'] = $History->uuid;
        $details['dt'] = $tempproduct->diamond_type ?? 1;
        return Excel::download(new HistoryExport($details), $name);
    }

    public function invaliddiamond(Request $request)
    {
        $User = Auth::guard('vendorapi')->user()->id;
        if($request->diamond_type == 1) {
            $product = DB::select("CALL AllProductImportErrorN('".$User."',0)");
        }
        if($request->diamond_type == 2) {
            $product = DB::select("CALL AllProductImportErrorL('".$User."',0)");
        }
        return response()->json(['success' => true, 'data' => ['result' => $product, 'message' => 'successfully.']], 200);
    }

    public function updateData(Request $request)
    {
        $User = Auth::guard('vendorapi')->user()->id;
        $data = json_decode($request->data);
        foreach($data as $key => $val) {
            $dd['stone_id'] = $val->stone_id;
            $dd['cert_no'] = $val->cert_no;
            $dd['cert_type'] = $val->cert_type;
            $dd['cert_url'] = $val->cert_url;
            $dd['image'] = $val->image;
            $dd['video'] = $val->video;
            $dd['carat'] = $val->carat;
            $dd['shape_id'] = $val->shape_id;
            $dd['color_id'] = $val->color_id;
            $dd['clarity_id'] = $val->clarity_id;
            $dd['cut_id'] = $val->cut_id;
            $dd['polish_id'] = $val->polish_id;
            $dd['symmetry_id'] = $val->symmetry_id;
            $dd['fluorescence_id'] = $val->fluorescence_id;
            $dd['rapo_rate'] = $val->rapo_rate;
            $dd['discount'] = $val->discount;
            $dd['rate'] = $val->rate;
            $dd['table_per'] = $val->table_per;
            $dd['depth_per'] = $val->depth_per;
            $dd['length'] = $val->length;
            $dd['width'] = $val->width;
            $dd['height'] = $val->height;
            $dd['country'] = $val->country;
            $TempProduct = TempProduct::find($val->id);
            $TempProduct->update($dd);
        }
        if($request->diamond_type == 1) {
            $product = DB::select("CALL AllProductImportErrorN('".$User."')");
        }
        if($request->diamond_type == 2) {
            $product = DB::select("CALL AllProductImportErrorL('".$User."')");
        }
        DB::select("CALL AllProductUpload('".$User."')");
        return response()->json(['success' => true, 'data' => ['result' => $product, 'message' => 'successfully.']], 200);
    }

    public function hold()
    {
        $User = Auth::guard('vendorapi')->user()->id;
        $inquiry = Product::where('status', 1)->where("hold",1)->where("confirm", 0)->where("vendor_id", $User)->get();
        return response()->json(['success' => true, 'data' => ['result' => $inquiry, 'message' => 'successfully.']], 200);
    }

    public function confirm()
    {
        $User = Auth::guard('vendorapi')->user()->id;
        $inquiry = Product::where('status', 1)->where("confirm", 1)->where("vendor_id", $User)->get();
        return response()->json(['success' => true, 'data' => ['result' => $inquiry, 'message' => 'successfully.']], 200);
    }
}
