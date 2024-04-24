<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\DiamondRequest;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function index()
    {
        $Slot = Slot::all();
        return response()->json(['success' => true, 'data' => ['result' => $Slot, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'day' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }
        $data['day'] = $request->day;
        $data['from_time'] = $request->from_time;
        $data['to_time'] = $request->to_time;
        $Slot = Slot::create($data);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Slot';
        $data1['LogType'] = 12;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Slot->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Slot, 'message' => 'successfully.']], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'day' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        $data['day'] = $request->day;
        $data['from_time'] = $request->from_time;
        $data['to_time'] = $request->to_time;
        $Slot = Slot::find($id);

        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Slot->name;
        $data['LName'] = 'Slot';
        $data['LogType'] = 12;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Slot = $Slot->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Slot, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = DiamondRequest::where('slot_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Slot belongs to Diamond Request in the system and cannot be deleted.']], 500);
        } else {
            $Slot = Slot::find($id);
            if($Slot){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Slot->name;
                $data1['LName'] = 'Delete Slot';
                $data1['LogType'] = 12;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Slot->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Slot deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Slot not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Slot = Slot::find($id);
        if($Slot){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Slot->name;
            $data['LName'] = 'Slot';
            $data['LogType'] = 9;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Slot = $Slot->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Slot, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Slot not found']], 500);
        }
    }

    public function RequestList()
    {
        $DiamondRequest = DiamondRequest::with(['product:id,stone_id,cert_no,cert_url,diamond_type,cert_type','slot'])->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondRequest, 'message' => 'successfully.']], 200);
    }

    public function Requestdelete(Request $request)
    {
        foreach(json_decode($request->request_id) as $request_id) {
            $DiamondRequest = DiamondRequest::find($request_id);
            if($DiamondRequest) {
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $DiamondRequest->product->stone_id;
                $data1['LName'] = 'Delete Diamond Request';
                $data1['LogType'] = 13;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $request_id;
                $data1['product_id'] = $DiamondRequest->product_id;
                event(new AdminLogStored($data1));
                $DiamondRequest->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Request deleted successfully.']], 200);
    }

    public function Requeststatus(Request $request, $id)
    {
        $DiamondRequest = DiamondRequest::find($id);
        if($DiamondRequest){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $DiamondRequest->product->stone_id;
            $data['LName'] = 'Diamond Request';
            $data['LogType'] = 13;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            $data['product_id'] = $DiamondRequest->product_id;
            event(new AdminLogStored($data));
            $DiamondRequest = $DiamondRequest->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $DiamondRequest, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond Request not found']], 500);
        }

        // DiamondRequest::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Request deleted successfully.']], 200);
    }
}
