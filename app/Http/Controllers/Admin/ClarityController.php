<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Clarity;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClarityController extends Controller
{
    public function index()
    {
        $Clarity = Clarity::all();
        return response()->json(['success' => true, 'data' => ['result' => $Clarity, 'message' => 'success']], 200);
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
        $data1['LName'] = 'Create Clarity';
        $data1['LogType'] = 3;
        $data1['AdminType'] = 1;
        $Clarity = Clarity::create($data);
        $data1['log_id'] = $Clarity->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Clarity, 'message' => 'successfully.']], 200);
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
        $Clarity = Clarity::find($id);

        $data['name'] = $request->name;
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Clarity->name;
        $data['LName'] = 'Clarity';
        $data['LogType'] = 3;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Clarity = $Clarity->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Clarity, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('clarity_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Clarity belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Clarity = Clarity::find($id);
            if($Clarity){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Clarity->name;
                $data1['LName'] = 'Delete Clarity';
                $data1['LogType'] = 3;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Clarity->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Clarity deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Clarity not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {

        $Clarity = Clarity::find($id);
        if($Clarity){
            $data1 = $request->all();
            $data1['log_entrytype'] = 1;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Clarity->name;
            $data1['LName'] = 'Clarity';
            $data1['LogType'] = 3;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $Clarity->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Clarity, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'User not found']], 500);
        }

        // $user = Clarity::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => $user, 'message' => 'Status change successfully.']], 200);
    }
}
