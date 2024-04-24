<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Marketing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarketingController extends Controller
{
    public function index()
    {
        $Marketing = Marketing::all();
        return response()->json(['success' => true, 'data' => ['result' => $Marketing, 'message' => 'success']], 200);
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
        if($request->has('image')){
            $file = $request->file('image');
            $filename = date('ymdHis').'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/image', $filename);
            $data['image'] = url('storage/image/'. $filename);
        }

        $Marketing = Marketing::where('type', $request->type)->first();
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $request->name;
        $data['LName'] = 'Marketing';
        $data['LogType'] = 11;
        $data['AdminType'] = 1;
        if($Marketing) {
            $data['log_id'] = $Marketing->id;
            event(new AdminLogStored($data));
            $Marketing = $Marketing->update($data);
        } else{
            $Marketing = Marketing::create($data);
            $data['log_id'] = $Marketing->id;
            event(new AdminLogStored($data));
        }

        // $Marketing = Marketing::updateOrCreate(
        //     ['type' => $request->type],
        //     $data);
        return response()->json(['success' => true, 'data' => ['result' => $Marketing, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Marketing = Marketing::find($id);
        if($Marketing){
            $data1 = [];
            $data1['log_entrytype'] = 0;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Marketing->name;
            $data1['LName'] = 'Delete Marketing';
            $data1['LogType'] = 11;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $Marketing->delete();
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Marketing deleted successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Marketing not found']], 500);
        }
    }

    public function status(Request $request, $id)
    {
        $Marketing = Marketing::find($id);
        $data = $request->all();
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Marketing->name;
        $data['LName'] = 'Marketing';
        $data['LogType'] = 11;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Marketing = $Marketing->update([
            'status' => $request->status
        ]);
        return response()->json(['success' => true, 'data' => ['result' => $Marketing, 'message' => 'Status change successfully.']], 200);
    }
}
