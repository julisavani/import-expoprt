<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\DiamondHold;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HoldController extends Controller
{
    public function index()
    {
        // $DiamondHold = DiamondHold::with('product')->get();
        $DiamondHold = DiamondHold::with('user:id,username,fullname')->groupBy('user_id')->select('user_id')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondHold, 'message' => 'successfully.']], 200);
    }
    public function user($id)
    {
        $DiamondHold = DiamondHold::with('product')->where('user_id', $id)->get();
        // $DiamondHold = DiamondHold::with('user:id,username,fullname')->groupBy('user_id')->select('user_id')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondHold, 'message' => 'successfully.']], 200);
    }

    public function delete(Request $request)
    {
        foreach(json_decode($request->hold_id) as $hold_id) {
            $DiamondHold = DiamondHold::find($hold_id);
            Product::find($DiamondHold->product_id)->update(['hold' => 0]);
            if($DiamondHold) {
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $DiamondHold->product->stone_id;
                $data1['LName'] = 'Delete Diamond Hold';
                $data1['LogType'] = 14;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $hold_id;
                $data1['product_id'] = $DiamondHold->product_id;
                event(new AdminLogStored($data1));
                $DiamondHold->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Hold deleted successfully.']], 200);
    }

    public function status(Request $request, $id)
    {
        $DiamondHold = DiamondHold::find($id);
        if($DiamondHold){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $DiamondHold->product->stone_id;
            $data['LName'] = 'Diamond Hold';
            $data['LogType'] = 14;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            $data['product_id'] = $DiamondHold->product_id;
            event(new AdminLogStored($data));
            $DiamondHold = $DiamondHold->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $DiamondHold, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond Hold not found']], 500);
        }
        // DiamondHold::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Hold deleted successfully.']], 200);
    }

    public function holdrelease()
    {
        $Hold = DiamondHold::where("created_at",">",Carbon::now()->subDay())->get();
        foreach($Hold as $key => $val) {
            Product::find($val->product_id)->update(['hold' => 0]);
            $data = [];
            $data['log_entrytype'] = 0;
            $data['user_id'] = null;
            $data['LogName'] = $val->product->stone_id ?? null;
            $data['LName'] = 'Delete Diamond Hold';
            $data['LogType'] = 14;
            $data['AdminType'] = 0;
            $data['log_id'] = $val->id;
            $data['product_id'] = $val->product_id;
            event(new AdminLogStored($data));
            $val->delete();
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'successfully.']], 200);
    }
}
