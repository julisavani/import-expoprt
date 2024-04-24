<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Demand;
use App\Models\DiamondInquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $DiamondInquiry = DiamondInquiry::with('user:id,username,fullname')->groupBy('user_id')->select('user_id')->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondInquiry, 'message' => 'successfully.']], 200);
    }
    public function user($id)
    {
        $DiamondInquiry = DiamondInquiry::with('product')->where('user_id', $id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $DiamondInquiry, 'message' => 'successfully.']], 200);
    }

    public function delete(Request $request)
    {
        // dd(json_decode($request->inquiry_id));
        foreach(json_decode($request->inquiry_id) as $Inquiry_id) {
            $DiamondInquiry = DiamondInquiry::find($Inquiry_id);
            if($DiamondInquiry) {
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $DiamondInquiry->name;
                $data1['LName'] = 'Delete Diamond Inquiry';
                $data1['LogType'] = 17;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $Inquiry_id;
                $data1['product_id'] = $DiamondInquiry->product_id;
                event(new AdminLogStored($data1));
                $DiamondInquiry->delete();
            }
        }
        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Inquiry deleted successfully.']], 200);
    }

    public function status(Request $request, $id)
    {
        $DiamondInquiry = DiamondInquiry::find($id);
        if($DiamondInquiry){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $DiamondInquiry->product->stone_id;
            $data['LName'] = 'Diamond Inquiry';
            $data['LogType'] = 17;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            $data['product_id'] = $DiamondInquiry->product_id;
            event(new AdminLogStored($data));
            $DiamondInquiry = $DiamondInquiry->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $DiamondInquiry, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Diamond Inquiry not found']], 500);
        }
        // DiamondInquiry::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Diamond Inquiry status successfully.']], 200);
    }

    // Appointment List
    public function appointmentindex()
    {
        $Appointment = Appointment::with('user')->get();
        return response()->json(['success' => true, 'data' => ['result' => $Appointment, 'message' => 'successfully.']], 200);
    }

    public function appointmentdelete($id)
    {
        $Appointment = Appointment::find($id);
        if($Appointment) {
            $data1 = [];
            $data1['log_entrytype'] = 0;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Appointment->date;
            $data1['LName'] = 'Delete Appointment';
            $data1['LogType'] = 18;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $Appointment->delete();
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Appointment deleted successfully.']], 200);
        }
        return response()->json(['success' => false, 'data' => ['result' => [], 'message' => 'Appointment not found.']], 422);
    }

    public function appointmentstatus(Request $request, $id)
    {
        $Appointment = Appointment::find($id);
        if($Appointment){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Appointment->date;
            $data['LName'] = 'Appointment';
            $data['LogType'] = 18;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Appointment = $Appointment->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Appointment, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Appointment not found']], 500);
        }
        // Appointment::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Appointment status successfully.']], 200);
    }

    // Demand List

    public function Demandindex()
    {
        $Demand = Demand::with('user')->get();
        return response()->json(['success' => true, 'data' => ['result' => $Demand, 'message' => 'successfully.']], 200);
    }

    public function Demanddelete($id)
    {
        $Demand = Demand::find($id);
        if($Demand) {
            $data1 = [];
            $data1['log_entrytype'] = 0;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Demand->name;
            $data1['LName'] = 'Delete Demand';
            $data1['LogType'] = 19;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $Demand->delete();
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Demand deleted successfully.']], 200);
        }
        return response()->json(['success' => false, 'data' => ['result' => [], 'message' => 'Demand not found.']], 422);
    }

    public function Demandstatus(Request $request, $id)
    {
        $Demand = Demand::find($id);
        if($Demand){
            $data = $request->all();
            $data['log_entrytype'] = 1;
            $data['user_id'] = auth()->guard('adminapi')->user()->id;
            $data['LogName'] = $Demand->name;
            $data['LName'] = 'Demand';
            $data['LogType'] = 19;
            $data['AdminType'] = 1;
            $data['log_id'] = $id;
            event(new AdminLogStored($data));
            $Demand = $Demand->update([
                'status' => $request->status
            ]);
            return response()->json(['success' => true, 'data' => ['result' => $Demand, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Demand not found']], 500);
        }
        // Demand::find($id)->update([
        //     'status' => $request->status
        // ]);
        // return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Demand status successfully.']], 200);
    }
}
