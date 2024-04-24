<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\RegisterMail;
use App\Mail\StatusMail;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function index()
    {
        $Vendor = Vendor::orderBy('id','desc')->get();
        return response()->json(['success' => true, 'data' => ['result' => $Vendor, 'message' => 'success']], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'email','unique:vendors,email'],
            'username' => ['required','unique:vendors,username']
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
        $data['password'] = Hash::make($request->password);
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
        $Vendors = Vendor::create($data);

        $hashid = Crypt::encryptString($Vendors->id);
        $details['subject'] = 'Confirm your email address to get started on Delight Diamond';
        $details['email'] = $request->email;
        $details['url'] = 'https://delightdiamonds.com/activation/vendor/' .$hashid;
        Mail::to($request->email)->send(new RegisterMail($details));

        $data1 = [];
        $data1['log_entrytype'] = 0;
        $data1['user_id'] = auth()->guard('adminapi')->user()->id;
        $data1['LogName'] = $request->username;
        $data1['LName'] = 'Create Vendor';
        $data1['LogType'] = 22;
        $data1['AdminType'] = 1;
        $data1['log_id'] = $Vendors->id;
        event(new AdminLogStored($data1));
        return response()->json(['success' => true, 'data' => ['result' => $Vendors, 'message' => 'successfully.']], 200);
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

        $Vendor = Vendor::find($id);
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
        $data['LogName'] = $Vendor->username;
        $data['LName'] = 'Vendor';
        $data['LogType'] = 22;
        $data['AdminType'] = 1;
        $data['log_id'] = $id;
        event(new AdminLogStored($data));
        $Vendor = $Vendor->update($data);
        return response()->json(['success' => true, 'data' => ['result' => $Vendor, 'message' => 'successfully.']], 200);
    }

    public function delete($id)
    {
        $DiamondConfirm = Product::where('vendor_id',$id)->first();
        if($DiamondConfirm){
            return response()->json(['success' => false, 'error' => ['message' => 'Sorry, this Diamond belongs to Diamond in the system and cannot be deleted.']], 500);
        } else {
            $Vendor = Vendor::findorfail($id);
            if($Vendor){
                $data1 = [];
                $data1['log_entrytype'] = 0;
                $data1['user_id'] = auth()->guard('adminapi')->user()->id;
                $data1['LogName'] = $Vendor->username;
                $data1['LName'] = 'Delete Vendor';
                $data1['LogType'] = 22;
                $data1['AdminType'] = 1;
                $data1['log_id'] = $id;
                event(new AdminLogStored($data1));
                $Vendor->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Vendor deleted successfully.']], 200);
            } else {
                return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Vendor not found']], 500);
            }
        }
    }

    public function status(Request $request, $id)
    {
        $Vendor = Vendor::find($id);
        if($Vendor){
            $data1 = $request->all();
            $data1['log_entrytype'] = 1;
            $data1['user_id'] = auth()->guard('adminapi')->user()->id;
            $data1['LogName'] = $Vendor->username;
            $data1['LName'] = 'Vendor';
            $data1['LogType'] = 22;
            $data1['AdminType'] = 1;
            $data1['log_id'] = $id;
            event(new AdminLogStored($data1));
            $Vendor->update([
                'status' => $request->status
            ]);

            $details['email'] = $Vendor->email;
            $details['subject'] = "Delight Diamond ". $Vendor->username ." Status chanded";
            Mail::to($Vendor->email)->send(new StatusMail($details));
            return response()->json(['success' => true, 'data' => ['result' => $Vendor, 'message' => 'Status change successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Vendor not found']], 500);
        }
    }
}
