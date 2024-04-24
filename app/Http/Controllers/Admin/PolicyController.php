<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PolicyController extends Controller
{
    public function index()
    {
        $Policy = Policy::all();
        return response()->json(['success' => true, 'data' => ['result' => $Policy, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
            'version' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $data['name'] = $request->name;
        $data['type'] = $request->type;
        $data['description'] = $request->description;
        $data['version'] = $request->version;

        $Policy = Policy::where('type', $request->type)->where('version', $request->version)->first();
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $request->name;
        $data['LName'] = 'Policy';
        $data['LogType'] = 10;
        $data['AdminType'] = 1;
        if($Policy) {
            $data['log_id'] = $Policy->id;
            event(new AdminLogStored($data));
            $Policy = $Policy->update($data);
        } else{
            $Policy = Policy::create($data);
            $data['log_id'] = $Policy->id;
            event(new AdminLogStored($data));
        }
        // $Policy = Policy::updateOrCreate(
        //     [
        //         'type' => $request->type,
        //         'version' => $request->version
        //     ],
        //     $data);
        return response()->json(['success' => true, 'data' => ['result' => $Policy, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Policy = Policy::find($id);
        $Product = User::where('version', $Policy->version)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Policy belongs to Users in the system and cannot be deleted.']], 500);
        } else {
            if($Policy){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Policy->name;
                $data1['LName'] = 'Delete Policy';
                $data1['LogType'] = 10;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Policy->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Policy deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Policy not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Policy = Policy::find($id);
        $data = $request->all();
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Policy->name;
        $data['LName'] = 'Policy';
        $data['LogType'] = 10;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Policy = $Policy->update([
            'status' => $request->status
        ]);
        return response()->json(['success' => true, 'data' => ['result' => $Policy, 'message' => 'Status change successfully.']], 200);
    }

}
