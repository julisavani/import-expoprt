<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
    public function index()
    {
        $Size = Size::all();
        return response()->json(['success' => true, 'data' => ['result' => $Size, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $data['name'] = $request->name;
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Size';
        $data1['LogType'] = 5;
        $data1['AdminType'] = 1;
        $Size = Size::create($data);
        $data1['log_id'] = $Size->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Size, 'message' => 'successfully.']], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        $data['name'] = $request->name;
        $Size = Size::find($id);
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Size->name;
        $data['LName'] = 'Size';
        $data['LogType'] = 5;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Size = $Size->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Size, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('size_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Size belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Size = Size::find($id);
            if($Size){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Size->name;
                $data1['LName'] = 'Delete Size';
                $data1['LogType'] = 5;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Size->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Size deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Size not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Size = Size::find($id);
        if($Size){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Size->name;
            $data['LName'] = 'Size';
            $data['LogType'] = 5;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Size = $Size->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Size, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Size not found']], 500);
        }
    }
}
