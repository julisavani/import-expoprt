<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShapeController extends Controller
{
    public function index()
    {
        $Shape = Shape::all();
        return response()->json(['success' => true, 'data' => ['result' => $Shape, 'message' => 'success']], 200);
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
        $data['icon'] = $request->icon;
        $Shape = Shape::create($data);

        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Shape';
        $data1['LogType'] = 6;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Shape->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Shape, 'message' => 'successfully.']], 200);
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
        $data['icon'] = $request->icon;
        $Shape = Shape::find($id);
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Shape->name;
        $data['LName'] = 'Shape';
        $data['LogType'] = 6;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Shape = $Shape->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Shape, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('shape_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Shape belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Shape = Shape::find($id);
            if($Shape){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Shape->name;
                $data1['LName'] = 'Delete Shape';
                $data1['LogType'] = 6;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Shape->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Shape deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Shape not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Shape = Shape::find($id);
        if($Shape){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Shape->name;
            $data['LName'] = 'Shape';
            $data['LogType'] = 6;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Shape = $Shape->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Shape, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Shape not found']], 500);
        }
    }
}
