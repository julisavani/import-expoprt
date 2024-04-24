<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Finish;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinishController extends Controller
{
    public function index()
    {
        $Finish = Finish::all();
        return response()->json(['success' => true, 'data' => ['result' => $Finish, 'message' => 'success']], 200);
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
        $data['specific_type'] = $request->specific_type;
        $Finish = Finish::create($data);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Finish';
        $data1['LogType'] = 8;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Finish->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Finish, 'message' => 'successfully.']], 200);
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
        $data['specific_type'] = $request->specific_type;
        $data['type'] = $request->type;
        $Finish = Finish::find($id);
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Finish->name;
        $data['LName'] = 'Finish';
        $data['LogType'] = 8;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Finish = $Finish->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Finish, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('cut_id', $id)->orwhere('polish_id', $id)->orwhere('symmetry_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Finish belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Finish = Finish::find($id);
            if($Finish){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Finish->name;
                $data1['LName'] = 'Delete Finish';
                $data1['LogType'] = 8;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Finish->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Finish deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Finish not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Finish = Finish::find($id);
        if($Finish){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Finish->name;
            $data['LName'] = 'Finish';
            $data['LogType'] = 8;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Finish = $Finish->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Finish, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Finish not found']], 500);
        }
    }
}
