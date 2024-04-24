<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\RegisterMail;
use App\Mail\StatusMail;
use App\Models\DiamondConfirm;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{
    public function index()
    {
        $Merchant = Merchant::orderBy('id','desc')->get();
        return response()->json(['success' => true, 'data' => ['result' => $Merchant, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'email','unique:merchants,email','unique:users,email'],
            'username' => ['required','unique:merchants,username','unique:users,username']
        ]);
        if($validator->fails()){
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Required Field missing', 'error_message' => $messages]], 422);
        }

        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['company'] = $request->company;
        $data['website'] = $request->website;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['mobile'] = $request->mobile;
        $data['other_phone'] = $request->other_phone;
        $data['about'] = $request->about;
        $data['job_title'] = $request->job_title;
        $data['business_type'] = json_encode($request->business_type);
        $data['buying_group'] = json_encode($request->buying_group);
        $data['group_title'] = $request->group_title ?? null;
        $data['address'] = $request->address;
        $data['city'] = $request->city;
        $data['state'] = $request->state;
        $data['pincode'] = $request->pincode;
        $data['country_id'] = $request->country_id;

        if($request->has('document')){
            $file = $request->file('document');
            $filename = str_replace(' ', '', $request->username.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/document', $filename);
            $data['document'] = $filename;
        }
        $Merchants = Merchant::create($data);
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make('kings@123'),
            'fullname' => $request->first_name.' '.$request->last_name,
            'country_id' => $request->country_id,
            'company' => $request->company,
            'mobile' => $request->mobile,
            'merchant_id' => $Merchants->id,
            'user_type' => 2
        ]);
        $hashid = Crypt::encryptString($Merchants->id);
        $details['subject'] = 'Confirm your email address to get started on Delight Diamond';
        $details['email'] = $request->email;
        $details['url'] = 'https://delightdiamonds.com/activation/merchant/' .$hashid;
        Mail::to($request->email)->send(new RegisterMail($details));

        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->username;
        $data1['LName'] = 'Create Merchant';
        $data1['LogType'] = 23;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Merchants->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Merchants, 'message' => 'successfully.']], 200);
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

        $Merchant = Merchant::find($id);
        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['company'] = $request->company;
        $data['website'] = $request->website;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['mobile'] = $request->mobile;
        $data['other_phone'] = $request->other_phone;
        $data['about'] = $request->about;
        $data['job_title'] = $request->job_title;
        $data['business_type'] = json_encode($request->business_type);
        $data['buying_group'] = json_encode($request->buying_group);
        $data['group_title'] = $request->group_title ?? null;
        $data['address'] = $request->address;
        $data['city'] = $request->city;
        $data['state'] = $request->state;
        $data['pincode'] = $request->pincode;
        $data['country_id'] = $request->country_id;

        if($request->has('document')){
            $file = $request->file('document');
            $filename = str_replace(' ', '', $request->username.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            $file->storeAs('public/document', $filename);
            $data['document'] = $filename;
        }
        $data['log_entrytype'] = 1;
        $data['user_id'] = auth()->guard('adminapi')->user()->id;
        $data['LogName'] = $Merchant->username;
        $data['LName'] = 'Merchant';
        $data['LogType'] = 23;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Merchant = $Merchant->update($data);
        return response()->json(['success' => true, 'data' => ['result' => $Merchant, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $User = User::where('merchant_id', $id)->where('user_type', 2)->first();
        if($User){
            $DiamondConfirm = DiamondConfirm::where('user_id',$User->id)->first();
            if($DiamondConfirm){
                return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Diamond belongs to Diamond Confirm in the system and cannot be deleted.']], 500);
            } else {
                $Merchant = Merchant::findorfail($id);
                // dd($Merchant);
                if($Merchant){
                    $data1 = [];
                    $data1['log_entrytype'] = 0;
                    $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                    $data1['LogName'] = $Merchant->username;
                    $data1['LName'] = 'Delete Merchant';
                    $data1['LogType'] = 23;
                    $data1['AdminType'] = 1;
                    $data1['log_id'] = $id;
                    event(new AdminLogStored($data1));
                    User::find($User->id)->delete();
                    $Merchant->delete();
                    return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Merchant deleted successfully.']], 200);
                } else {
                    return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Merchant not found']], 500);
                }
            }
        } else{
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Merchant not found']], 500);
        }
    }

    public function status(Request $request, $id)
    {
        $Merchant = Merchant::find($id);
        if($Merchant){
            $data1 = $request->all();
            $data1['log_entrytype'] = 1;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Merchant->username;
            $data1['LName'] = 'Merchant';
            $data1['LogType'] = 23;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $Merchant->update([
                'status' => $request->status
            ]);

            $details['email'] = $Merchant->email;
            $details['subject'] = "Delight Diamond ". $Merchant->username ." Status chanded";
            Mail::to($Merchant->email)->send(new StatusMail($details));
            return response()->json(['success' => true, 'data' => ['result' => $Merchant, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Merchant not found']], 500);
        }
    }

}
