<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    public function index()
    {
        $Color = Color::all();
        return response()->json(['success' => true, 'data' => ['result' => $Color, 'message' => 'success']], 200);
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
        $Color = Color::create($data);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Color';
        $data1['LogType'] = 4;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Color->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Color, 'message' => 'successfully.']], 200);
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
        $Color = Color::find($id);
        $data['name'] = $request->name;
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Color->name;
        $data['LName'] = 'Color';
        $data['LogType'] = 4;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Color = $Color->update($data);

        return response()->json(['success' => true, 'data' => ['result' => $Color, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $Product = Product::where('color_id', $id)->first();
        if($Product){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Color belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Color = Color::find($id);
            if($Color){
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('adminapi')->user()->id;
                $data['LogName'] = $Color->name;
                $data['LName'] = 'Delete Color';
                $data['LogType'] = 4;
                $data['AdminType'] = 1;
                $data['log_id'] = $id;
                event(new AdminLogStored($data));
                $Color->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Color deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Color not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Color = Color::find($id);
        if($Color){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Color->name;
            $data['LName'] = 'Color';
            $data['LogType'] = 4;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Color->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Color, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Color not found']], 500);
        }
        // $user = Color::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => $user, 'message' => 'Status change successfully.']], 200);
    }
}
