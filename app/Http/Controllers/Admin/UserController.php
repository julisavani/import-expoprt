<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\StatusMail;
use App\Models\AdminLog;
use App\Models\Appointment;
use App\Models\Country;
use App\Models\Demand;
use App\Models\DiamondCart;
use App\Models\DiamondConfirm;
use App\Models\DiamondHold;
use App\Models\DiamondInquiry;
use App\Models\DiamondRequest;
use App\Models\SaveSearch;
use App\Models\StarMeleeInquiry;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $User = User::whereNull('merchant_id')->orderBy('id','desc')->get();
        return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'email','unique:users,email'],
            'username' => ['required','unique:users,username'],
            'password' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 422);
        }
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['fullname'] = $request->fullname;
        $data['country_id'] = $request->country_id;
        $data['company'] = $request->company;
        $data['mobile'] = $request->mobile;
        $data['whatsapp'] = $request->whatsapp;
        $data['wechat'] = $request->wechat;
        $data['skype'] = $request->skype;

        if($request->has('document')){
            $file = $request->file('document');
            $filename = str_replace(' ', '', $request->username.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/document', $filename);
            $data['document'] = $filename;
        }
        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->username;
        $data1['LName'] = 'Create User';
        $data1['LogType'] = 2;
        $data1['AdminType'] = 1;
        $User = User::create($data);
        $data1['log_id'] = $User->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'successfully.']], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 500);
        }

        $User = User::find($id);
        $data['username'] = $request->username;
        $data['fullname'] = $request->fullname;
        $data['country_id'] = $request->country_id;
        $data['company'] = $request->company;
        $data['mobile'] = $request->mobile;
        $data['whatsapp'] = $request->whatsapp;
        $data['wechat'] = $request->wechat;
        $data['skype'] = $request->skype;

        if($request->hasfile('document')){
            $file = $request->file('document');
            $filename = str_replace(' ', '', $request->username.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/document', $filename);
            $data['document'] = $filename;
        }
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $User->username;
        $data['LName'] = 'User';
        $data['LogType'] = 2;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $User = $User->update($data);
        return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        // $DiamondConfirm = DiamondConfirm::where('user_id',$id)->first();
        // if($DiamondConfirm){
        //     return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Diamond belongs to Diamond Confirm in the system and cannot be deleted.']], 500);
        // } else {
            $user = User::findorfail($id);
            if($user){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $user->username;
                $data1['LName'] = 'Delete User';
                $data1['LogType'] = 2;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                AdminLog::where('user_id', $user->id)->delete();
                Appointment::where('user_id', $user->id)->delete();
                DiamondCart::where('user_id', $user->id)->withTrashed()->forceDelete();
                DiamondRequest::where('user_id', $user->id)->withTrashed()->forceDelete();
                DiamondInquiry::where('user_id', $user->id)->withTrashed()->forceDelete();
                DiamondHold::where('user_id', $user->id)->withTrashed()->forceDelete();
                Demand::where('user_id', $user->id)->withTrashed()->forceDelete();
                StarMeleeInquiry::where('user_id', $user->id)->delete();
                DiamondConfirm::where('user_id', $user->id)->withTrashed()->forceDelete();
                SaveSearch::where('id',$user->id)->withTrashed()->forceDelete();
                User::where('id',$user->id)->forceDelete();
                // $User->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'User deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'User not found']], 500);
            }
        // }
    }

    public function status(Request $request, $id)
    {
        $User = User::find($id);
        if($User){
            $data1 = $request->all();
            $data1['log_entrytype'] = 1;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $User->username;
            $data1['LName'] = 'User';
            $data1['LogType'] = 2;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $User->update([
                'status' => $request->status
            ]);

            $details['email'] = $User->email;
            $details['subject'] = "Delight Diamond ". $User->username ." Status chanded";
            Mail::to($User->email)->send(new StatusMail($details));
            return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'User not found']], 500);
        }
    }

    public function country()
    {
        $Country = Country::all();
        $data['BusinessType'] = Vendor::$BusinessType;
        $data['ByuingGroup'] = Vendor::$ByuingGroup;
        return response()->json(['success' => true, 'data' => ['result' => $Country, 'data'=> $data, 'message' => 'success']], 200);
    }


}
