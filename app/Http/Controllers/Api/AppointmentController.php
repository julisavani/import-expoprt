<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    // Add to Inquiry List
    public function index()
    {
        $User = Auth::guard('userapi')->user();
        $Appointment = Appointment::where('user_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $Appointment, 'message' => 'successfully.']], 200);
    }

    public function store(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $validator = Validator::make($request->all(),[
            'date' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        $Appointment = Appointment::create([
            'user_id' => $User->id,
            'date' => $request->date,
            'time' => $request->time,
            'remark' => $request->remark,
            'status' => 0,
        ]);

        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = $User->id;
        $data1['LogName'] = $request->date;
        $data1['LName'] = 'Create Appointment';
        $data1['LogType'] = 18;
        $data1['AdminType'] = 0;
        $data1['log_id'] = $Appointment->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Appointment, 'message' => 'Appointment created successfully.']], 200);
    }

    public function delete(Request $request)
    {
        foreach(json_decode($request->appointment_id) as $appointment_id) {
            $Appointment = Appointment::find($appointment_id);
            if($Appointment) {
                $data = [];
                $data['log_entrytype'] = 0;
                $data['user_id'] = auth()->guard('userapi')->user()->id;
                $data['LogName'] = $Appointment->date;
                $data['LName'] = 'Delete Appointment';
                $data['LogType'] = 18;
                $data['AdminType'] = 0;
                $data['log_id'] = $appointment_id;
                event(new AdminLogStored($data));
                $Appointment->delete();
            }
        }
        // $Appointment = Appointment::find($id);
        // if($Appointment) {
        //     $Appointment->delete();
        // }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Appointment deleted successfully.']], 200);
    }
}
