<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Demand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandController extends Controller
{
    public function index()
    {
        $User = Auth::guard('userapi')->user();
        $Demand = Demand::where('user_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $Demand, 'message' => 'successfully.']], 200);
    }

    public function store(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $data = json_encode($request->all());
        $Demand = Demand::create([
            'user_id' => $User->id,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'demand' => $data
        ]);
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = $User->id;
        $data1['LogName'] = $request->name;
        $data1['LName'] = 'Create Demand';
        $data1['LogType'] = 19;
        $data1['AdminType'] = 0;
        $data1['log_id'] = $Demand->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Demand, 'message' => 'successfully.']], 200);
    }

    public function delete(Request $request)
    {
        foreach(json_decode($request->demand_id) as $demand_id) {
            $Demand = Demand::find($demand_id);
            if($Demand) {
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $Demand->name;
                $data['LName'] = 'Delete Demand';
                $data['LogType'] = 19;
                $data['AdminType'] = 0;
                $data['log_id'] = $demand_id;
                event(new AdminLogStored($data));
                $Demand->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Demand deleted successfully.']], 200);
        // $Demand = Demand::find($id);
        // if($Demand){
        //     $Demand->delete();
        //     return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Demand deleted successfully.']], 200);
        // } else {
        //     return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Demand not found']], 500);
        // }
    }
}
