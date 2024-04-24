<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Fluorescence;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FluorescenceController extends Controller
{
    public function index()
    {
        $Fluorescence = Fluorescence::all();
        return response()->json(['success' => true, 'data' => ['result' => $Fluorescence, 'message' => 'success']], 200);
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
        $data['type'] = $request->type;
        $Fluorescence = Fluorescence::create($data);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Fluorescence';
        $data1['LogType'] = 9;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Fluorescence->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Fluorescence, 'message' => 'successfully.']], 200);
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
        $data['type'] = $request->type;
        $Fluorescence = Fluorescence::find($id);
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Fluorescence->name;
        $data['LName'] = 'Fluorescence';
        $data['LogType'] = 9;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Fluorescence = $Fluorescence->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Fluorescence, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('bgm_id', $id)->orwhere('fluorescence_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Fluorescence belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Fluorescence = Fluorescence::find($id);
            if($Fluorescence){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Fluorescence->name;
                $data1['LName'] = 'Delete Fluorescence';
                $data1['LogType'] = 9;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Fluorescence->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Fluorescence deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Fluorescence not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Fluorescence = Fluorescence::find($id);
        if($Fluorescence){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Fluorescence->name;
            $data['LName'] = 'Fluorescence';
            $data['LogType'] = 9;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Fluorescence = $Fluorescence->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Fluorescence, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Fluorescence not found']], 500);
        }
    }
}
