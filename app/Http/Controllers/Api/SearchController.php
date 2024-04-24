<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Clarity;
use App\Models\Color;
use App\Models\FancyColor;
use App\Models\Finish;
use App\Models\Fluorescence;
use App\Models\Product;
use App\Models\SaveSearch;
use App\Models\Shape;
use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function master()
    {
        $result['Shape'] = Shape::where('status',1)->get();
        $result['Color'] = Color::where('status',1)->get();
        $result['Clarity'] = Clarity::where('status',1)->get();
        $result['Size'] = Size::where('status',1)->get();
        $result['FancyColor'] = FancyColor::where('status',1)->get();
        $result['Finish'] = Finish::where('status',1)->get();
        $result['Fluorescence'] = Fluorescence::where('status',1)->get();
        $result['Lab'] = Product::$Lab;
        $result['DiamondType'] = Product::$DiamondType;
        $result['Type'] = Product::$Type;
        $result['Location'] = Product::select('country')->distinct('country')->get();
        $result['key_to_symbols'] = Product::select('key_to_symbols')->distinct('key_to_symbols')->get();
        $result['milky'] = Product::select('milky')->distinct('milky')->get();
        $result['table_open'] = Product::select('table_open')->distinct('table_open')->get();
        $result['white_table'] = Product::select('white_table')->distinct('white_table')->get();
        $result['table_black'] = Product::select('table_black')->distinct('table_black')->get();
        $result['white_side'] = Product::select('white_side')->distinct('white_side')->get();
        $result['side_black'] = Product::select('side_black')->distinct('side_black')->get();
        return response()->json(['success' => true, 'data' => ['result' => $result, 'message' => 'successfully.']], 200);
    }

    public function search(Request $request)
    {
        $Product = Product::where('status', 1)->where('confirm',0);
        $User = Auth::guard('userapi')->user();
        $data = $request->all();

        $Product = $Product->where('diamond_type', $User->diamond_type);

        if($request->shape_id != null) {
            $shape_id = json_decode($request->shape_id);
            $Product = $Product->whereIn('shape_id', $shape_id);
        }
        if($request->carat_from != null) {
            $carat_to = json_decode($request->carat_to);
            foreach(json_decode($request->carat_from) as $key => $carat_from) {
                $Product = $Product->whereBetween('carat', [$carat_from, $carat_to[$key]]);
            }
        }
        if($request->size_id != null) {
            $size_id = json_decode($request->size_id);
            $Product = $Product->whereIn('size_id', $size_id);
        }
        if($request->color_id != null) {
            $color_id = json_decode($request->color_id);
            $Product = $Product->whereIn('color_id', $color_id);
        }
        if($request->colors_id != null) {
            $colors_id = json_decode($request->colors_id);
            $Product = $Product->whereIn('colors_id', $colors_id);
        }
        if($request->overtone_id != null) {
            $overtone_id = json_decode($request->overtone_id);
            $Product = $Product->whereIn('overtone_id', $overtone_id);
        }
        if($request->intensity_id != null) {
            $intensity_id = json_decode($request->intensity_id);
            $Product = $Product->whereIn('intensity_id', $intensity_id);
        }
        if($request->clarity_id != null) {
            $clarity_id = json_decode($request->clarity_id);
            $Product = $Product->whereIn('clarity_id', $clarity_id);
        }

        if($request->cut_id != null) {
            $cut_id = json_decode($request->cut_id);
            $Product = $Product->whereIn('cut_id', $cut_id);
        }
        if($request->polish_id != null) {
            $polish_id = json_decode($request->polish_id);
            $Product = $Product->whereIn('polish_id', $polish_id);
        }
        if($request->symmetry_id != null) {
            $symmetry_id = json_decode($request->symmetry_id);
            $Product = $Product->whereIn('symmetry_id', $symmetry_id);
        }
        if($request->fluorescence_id != null) {
            $fluorescence_id = json_decode($request->fluorescence_id);
            $Product = $Product->whereIn('fluorescence_id', $fluorescence_id);
        }
        if($request->fluorescence_color_id != null) {
            $fluorescence_color_id = json_decode($request->fluorescence_color_id);
            $Product = $Product->whereIn('fluorescence_color_id', $fluorescence_color_id);
        }
        if($request->bgm_id != null) {
            $bgm_id = json_decode($request->bgm_id);
            $Product = $Product->whereIn('bgm_id', $bgm_id);
        }
        if($request->lab != null) {
            $lab = json_decode($request->lab);
            $Product = $Product->where('cert_type', $lab);
        }

        if($request->ct_from != null) {
            $Product = $Product->whereBetween('rate', [$request->ct_from, $request->ct_to]);
        }
        if($request->discount_from != null) {
            $Product = $Product->whereBetween('discount', [$request->discount_from, $request->discount_to]);
        }
        if($request->value_from != null) {
            $Product = $Product->whereBetween('amount', [$request->value_from, $request->value_to]);
        }

        if($request->stone_id != null) {
            $stone_id = $request->stone_id;
            $Product = $Product->where(function ($query) use($stone_id) {
                $query->where('stone_id', $stone_id)->orWhere('cert_no', $stone_id);
            });
        }

        if($request->location != null) {
            $location = json_decode($request->location);
            $Product = $Product->whereIn('country', $location);
        }

        if($request->length_from != null) {
            $Product = $Product->whereBetween('length', [$request->length_from, $request->length_to]);
        }

        if($request->width_from != null) {
            $Product = $Product->whereBetween('width', [$request->width_from, $request->width_to]);
        }

        if($request->height_from != null) {
            $Product = $Product->whereBetween('height', [$request->height_from, $request->height_to]);
        }

        if($request->table_from != null) {
            $Product = $Product->whereBetween('table', [$request->table_from, $request->table_to]);
        }

        if($request->depth_from != null) {
            $Product = $Product->whereBetween('depth', [$request->depth_from, $request->depth_to]);
        }

        if($request->ratio_from != null) {
            $Product = $Product->whereBetween('ratio', [$request->ratio_from, $request->ratio_to]);
        }

        if($request->table_open != null) {
            $table_open = json_decode($request->table_open);
            $Product = $Product->whereIn('table_open', $table_open);
        }

        if($request->white_table != null) {
            $white_table = json_decode($request->white_table);
            $Product = $Product->whereIn('white_table', $white_table);
        }

        if($request->table_black != null) {
            $table_black = json_decode($request->table_black);
            $Product = $Product->whereIn('table_black', $table_black);
        }
        if($request->white_side != null) {
            $white_side = json_decode($request->white_side);
            $Product = $Product->whereIn('white_side', $white_side);
        }
        if($request->side_black != null) {
            $side_black = json_decode($request->side_black);
            $Product = $Product->whereIn('side_black', $side_black);
        }
        if($request->eye_clean != null && $request->eye_clean != 0) {
            $Product = $Product->where('eye_clean', 'YES');
        }
        if($request->h_a != null && $request->h_a != 0) {
            $Product = $Product->where('h_a', $request->h_a);
        }
        if($request->no_bgm != null && $request->no_bgm != 0) {
            $Product = $Product->where('no_bgm', $request->no_bgm);
        }
        //
        $count = $Product->get()->count();
        $Totalamount = $Product->sum('amount');
        $Totalcarat = $Product->sum('carat');
        $Totaldiscount  = $Product->sum('discount');
        $ProductIds = $Product->pluck('id')->toArray();
        $Product = $Product->skip($data['$skip'] == NULL || $data['$skip'] == "null" ? 0 : $data['$skip'])->take($data['$top'])->get();
        return response()->json(['success' => true, 'data' => ['result' => $Product, 'count' => $count,  'ProductIds' => $ProductIds , 'Totalamount' => $Totalamount , 'Totalcarat' => $Totalcarat , 'Totaldiscount' => $Totaldiscount , 'message' => 'successfully.']], 200);
    }

    public function mainsearch(Request $request)
    {
        $Product = Product::where('status', 1)->where('confirm',0);
        $stone_id = $request->stone_id;
        $Product = $Product->where(function ($query) use($stone_id) {
            $query->where('stone_id', $stone_id)->orWhere('cert_no', $stone_id);
        });
        $Product = $Product->get();
        return response()->json(['success' => true, 'data' => ['result' => $Product, 'message' => 'successfully.']], 200);
    }

    public function newarrival(Request $request)
    {
        $data = $request->all();
        $Product = Product::where('status', 1)->where('confirm',0)->where("created_at",">",Carbon::now()->subDay());
        $total = $Product->get()->count();
        $Product = $Product->skip($data['$skip'] == NULL || $data['$skip'] == "null" ? 0 : $data['$skip'])->take($data['$top'])->get();
        return response()->json(['success' => true, 'data' => ['result' => $Product, 'count' => $total, 'message' => 'successfully.']], 200);
    }

    public function savesearch(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $data = json_encode($request->all());
        $SaveSearch = SaveSearch::create([
            'user_id' => $User->id,
            'name' => $request->name,
            'search' => $data
        ]);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = $User->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Saved Search';
        $data1['LogType'] = 20;
        $data1['AdminType'] = 0;
        $data1['log_id'] = $SaveSearch->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $SaveSearch, 'message' => 'successfully.']], 200);
    }

    public function list()
    {
        $User = Auth::guard('userapi')->user();
        $SaveSearch = SaveSearch::where('user_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $SaveSearch, 'message' => 'successfully.']], 200);
    }

    public function delete(Request $request)
    {
        foreach(json_decode($request->search_id) as $search_id) {
            $SaveSearch = SaveSearch::find($search_id);
            if($SaveSearch) {
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = Auth::guard('userapi')->user()->id;
                $data1['LogName'] = $request->name;
                $data1['LName'] = 'Delete Saved Search';
                $data1['LogType'] = 20;
                $data1['AdminType'] = 0;
                $data1['log_id'] = $SaveSearch->id;
                event(new AdminLogStored($data1));
                $SaveSearch->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Save Search deleted successfully.']], 200);
        // $SaveSearch = SaveSearch::find($id);
        // if($SaveSearch){
        //     $SaveSearch->delete();
        //     return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'SaveSearch deleted successfully.']], 200);
        // } else {
        //     return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'SaveSearch not found']], 500);
        // }
    }
}
