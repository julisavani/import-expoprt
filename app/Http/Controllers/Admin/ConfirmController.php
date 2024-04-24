<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\DiamondConfirm;
use App\Models\Product;
use Illuminate\Http\Request;

class ConfirmController extends Controller
{
    // public function index()
    // {
    //     $DiamondConfirm = DiamondConfirm::with('product')->get();
    //     return response()->json(['success' => true, 'data' => ['result' => $DiamondConfirm, 'message' => 'successfully.']], 200);
    // }

    public function index()
    {
        $DiamondConfirm = DiamondConfirm::with('user:id,username,fullname')->groupBy('user_id')->select('user_id')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondConfirm, 'message' => 'successfully.']], 200);
    }
    public function user($id)
    {
        $DiamondConfirm = DiamondConfirm::with('product')->where('user_id', $id)->where('status', 1)->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondConfirm, 'message' => 'successfully.']], 200);
    }

    public function delete(Request $request)
    {
        foreach(json_decode($request->confirm_id) as $confirm_id) {
            $DiamondConfirm = DiamondConfirm::find($confirm_id);
            if($DiamondConfirm) {
                Product::find($DiamondConfirm->product_id)->update(['confirm' => 0]);
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $DiamondConfirm->product->stone_id;
                $data1['LName'] = 'Delete Diamond Confirm';
                $data1['LogType'] = 16;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $confirm_id;
                $data1['product_id'] = $DiamondConfirm->product_id;
                event(new AdminLogStored($data1));
                $DiamondConfirm->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Confirm deleted successfully.']], 200);
    }

    public function status(Request $request, $id)
    {
        $DiamondConfirm = DiamondConfirm::find($id);
        if($DiamondConfirm){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $DiamondConfirm->product->stone_id;
            $data['LName'] = 'Diamond Confirm';
            $data['LogType'] = 16;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            $data['product_id'] = $DiamondConfirm->product_id;
            event(new AdminLogStored($data));
            $DiamondConfirm = $DiamondConfirm->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $DiamondConfirm, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond Confirm not found']], 500);
        }
        // DiamondConfirm::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Confirm status changed successfully.']], 200);
    }

    public function release(Request $request)
    {
        foreach(json_decode($request->confirm_id) as $confirm_id) {
            $DiamondConfirm = DiamondConfirm::find($confirm_id);
            if($DiamondConfirm) {
                Product::find($DiamondConfirm->product_id)->update(['confirm' => 0]);
                DiamondConfirm::find($confirm_id)->delete();
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $DiamondConfirm->name;
                $data1['LName'] = 'Release Diamond Confirm';
                $data1['LogType'] = 16;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $confirm_id;
                $data1['product_id'] = $DiamondConfirm->product_id;
                event(new AdminLogStored($data1));
                $DiamondConfirm->update([
                    'status' => 0
                ]);
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Confirm released successfully.']], 200);
    }
}
