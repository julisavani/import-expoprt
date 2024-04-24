<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\FancyColor;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FancyColorController extends Controller
{
    public function index()
    {
        $FancyColor = FancyColor::all();
        return response()->json(['success' => true, 'data' => ['result' => $FancyColor, 'message' => 'success']], 200);
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
        if($request->has('image')){
            $file = $request->file('image');
            $filename = str_replace(' ', '', $request->name.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/fancycolor', $filename);
            $data['image'] = url('storage/fancycolor/'. $filename);
        }
        $data['name'] = $request->name;
        $data['type'] = $request->type;
        $FancyColor = FancyColor::create($data);

        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Fancy Color';
        $data1['LogType'] = 7;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $FancyColor->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $FancyColor, 'message' => 'successfully.']], 200);
    }

    public function update(Request $request, $id)
    {
        $FancyColor = FancyColor::findOrFail($id);
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
            'image' => ['nullable','image']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = str_replace(' ', '', $request->name.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/fancycolor', $filename);
            $data['image'] = url('storage/fancycolor/'. $filename);
        }
        $data['name'] = $request->name;
        $data['type'] = $request->type;
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $FancyColor->name;
        $data['LName'] = 'Fancy Color';
        $data['LogType'] = 7;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $FancyColor = $FancyColor->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $FancyColor, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('colors_id', $id)->orwhere('overtone_id', $id)->orwhere('intensity_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Fancy Color belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $FancyColor = FancyColor::find($id);
            if($FancyColor){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $FancyColor->name;
                $data1['LName'] = 'Delete Fancy Color';
                $data1['LogType'] = 7;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $FancyColor->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'FancyColor deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'FancyColor not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $FancyColor = FancyColor::find($id);
        if($FancyColor){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $FancyColor->name;
            $data['LName'] = 'Fancy Color';
            $data['LogType'] = 7;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $FancyColor = $FancyColor->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $FancyColor, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Fancy Color not found']], 500);
        }
    }
}
