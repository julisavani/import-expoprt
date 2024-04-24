<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\DiamondCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // public function index()
    // {
    //     $DiamondCart = DiamondCart::with('product')->get();
    //     return response()->json(['success' => true, 'data' => ['result' => $DiamondCart, 'message' => 'successfully.']], 200);
    // }

    public function index()
    {
        $DiamondCart = DiamondCart::with('user:id,username,fullname')->groupBy('user_id')->select('user_id')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondCart, 'message' => 'successfully.']], 200);
    }
    public function user($id)
    {
        $DiamondCart = DiamondCart::with('product')->where('user_id', $id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondCart, 'message' => 'successfully.']], 200);
    }

    public function delete(Request $request)
    {
        foreach(json_decode($request->cart_id) as $cart_id) {
            $DiamondCart = DiamondCart::find($cart_id);
            if($DiamondCart) {
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $DiamondCart->product->stone_id;
                $data1['LName'] = 'Delete Diamond Cart';
                $data1['LogType'] = 15;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $cart_id;
                $data1['product_id'] = $DiamondCart->product_id;
                event(new AdminLogStored($data1));
                $DiamondCart->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Cart deleted successfully.']], 200);
    }

    public function status(Request $request, $id)
    {
        $DiamondCart = DiamondCart::find($id);
        if($DiamondCart){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $DiamondCart->product->stone_id;
            $data['LName'] = 'Diamond Cart';
            $data['LogType'] = 15;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            $data['product_id'] = $DiamondCart->product_id;
            event(new AdminLogStored($data));
            $DiamondCart = $DiamondCart->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $DiamondCart, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond Cart not found']], 500);
        }
        // DiamondCart::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Cart deleted successfully.']], 200);
    }
}
