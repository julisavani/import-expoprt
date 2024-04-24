<?php

namespace App\Http\Controllers\Vendor;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\RegisterMail;
use App\Mail\OTP as MailOTP;
use App\Models\Country;
use App\Models\Otp;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('vendor', ['except' => ['login','register','country','forgotpassword','verifyotp','updatepassword','activation']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'email','unique:vendors,email'],
            'username' => ['required','unique:vendors,username'],
            'password' => ['required']
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
        $data['password'] = Hash::make($request->password);
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
        $Vendors = Vendor::create($data);
        $hashid = Crypt::encryptString($Vendors->id);
        $details['subject'] = 'Confirm your email address to get started on Delight Diamond';
        $details['email'] = $request->email;
        $details['url'] = 'https://delightdiamonds.com/vendor/activation/' .$hashid;
        Mail::to($request->email)->send(new RegisterMail($details));

        return response()->json(['success' => true, 'data' => ['result' => $Vendors, 'message' => 'successfully.']], 200);
    }

    public function activation($id)
    {
        $hashid1 = Crypt::decryptString($id);
        $Vendor = Vendor::find($hashid1);
        if($Vendor) {
            Vendor::find($hashid1)->update([
                'verified_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Email verified successfully']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid token please try again']], 401);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Required field missing']], 422);
        }
        $input = $request->only('password');
        $jwt_token = null;
        $input['status'] = 1;
        if(filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $input['email'] = $request->email;
        } else {
            $input['username'] = $request->email;
        }

        if (!$jwt_token = Auth::guard('vendorapi')->attempt($input)) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid Email/Username or Password']], 401);
        }

        $user = Auth::guard('vendorapi')->user();
        return response()->json(['success' => true, 'data' => ['result' => $user, 'token_type' => 'Bearer', 'token' => $jwt_token, 'message' => 'successfully.']], 200);
    }

    public function profile()
    {
        try {
            $Vendor = Auth::guard('vendorapi')->user();
            return response()->json(['success' => true, 'data' => ['result' => $Vendor, 'message' => 'successfully.']], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => $e->getMessage()]], 500);
        }
    }

    public function updateprofile(Request $request)
    {
        $rules = [
            'email'   => 'required|email'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => $messages]], 500);
        }

        $Vendor = Auth::guard('vendorapi')->user();

        $Vendor->first_name = $request->first_name;
        $Vendor->last_name = $request->last_name;
        $Vendor->company = $request->company;
        $Vendor->website = $request->website;
        $Vendor->username = $request->username;
        $Vendor->email = $request->email;
        $Vendor->password = Hash::make($request->password);
        $Vendor->mobile = $request->mobile;
        $Vendor->other_phone = $request->other_phone;
        $Vendor->about = $request->about;
        $Vendor->job_title = $request->job_title;
        $Vendor->business_type = $request->business_type;
        $Vendor->buying_group = $request->buying_group;
        $Vendor->group_title = $request->group_title ?? null;
        $Vendor->address = $request->address;
        $Vendor->city = $request->city;
        $Vendor->state = $request->state;
        $Vendor->pincode = $request->pincode;
        $Vendor->country_id = $request->country_id;

        // $data = $request->all();
        // $data['log_entrytype'] = 1;
        // $data['user_id'] = $Vendor->id;
        // $data['LogName'] = $Vendor->username;
        // $data['LName'] = 'Vendor';
        // $data['LogType'] = 1;
        // $data['AdminType'] = 0;
        // $data['log_id'] = $Vendor->id;
        // event(new AdminLogStored($data));
        $Vendor->save();
        return response()->json(['success' => true, 'data' => ['result' => $Vendor, 'message' => 'successfully.']], 200);
    }

    public function forgotpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Required field missing']], 422);
        }
        $otp = rand(1111, 9999);
        $user = Vendor::Where('email', $request->email)->first();
        if ($user) {
            $data = $request->all();
            $data['otp'] = $otp;
            $otpObj = Otp::where('email', $request->email)->where('otptype', 3)->first();
            if ($otpObj) {
                $otpObj->update(['otp' => $otp]);
            } else {
                Otp::create(['email' => $request->email, 'otp' => $otp, 'otptype' => 3]);
                $details['otp'] = $otp;
                $details['email'] = $request->email;
                Mail::to($request->email)->send(new MailOTP($details));
            }
            return response()->json(['success' => true, 'data' => ['result' => $otp, 'message' => 'successfully.']], 200);
        } else {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Email not found']], 422);
        }
    }

    public function verifyotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Required field missing']], 422);
        }

        $otp = Otp::where('email', $request->email)->where('otptype', 3)->first();
        if($otp){
            if ($otp->otp == $request->otp) {
                $otp->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'otp verifid successfully.']], 200);
            }
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid OTP']], 422);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Vendor not found']], 422);
    }

    public function updatepassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Required field missing']], 422);
        }
        $email = $request->email;
        $user = Vendor::Where('email', $email)->first();
        if ($user) {
            $password = Hash::make($request->password);
            $user->update(['password' => $password]);
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'password change successfully.']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Vendor not found']], 422);
    }

    public function logout()
    {
        Auth::guard('vendorapi')->logout();
        return response()->json(['success' => true, 'data' => ['message' => 'Log out successfully.']], 200);
    }

    public function changepassword(Request $request)
    {
        $Profile = Auth::guard('vendorapi')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        #Match The Old Password
        if(!Hash::check($request->old_password, $Profile->password)){
            return response()->json(['success' => false, 'error' => ['message' => "Old Password Doesn't match!."]], 422);
        }

        #Update the new Password
        Vendor::find($Profile->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        // $data = $request->all();
        // $data['log_entrytype'] = 0;
        // $data['user_id'] = $Profile->id;
        // $data['LogName'] = $Profile->username;
        // $data['LName'] = 'Vendor Change Password';
        // $data['LogType'] = 1;
        // $data['AdminType'] = 0;
        // $data['log_id'] = $Profile->id;
        // event(new AdminLogStored($data));

        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Password changed successfully!.']], 200);
    }

    public function country()
    {
        $data['country'] = Country::all();
        $data['BusinessType'] = Vendor::$BusinessType;
        $data['ByuingGroup'] = Vendor::$ByuingGroup;
        return response()->json(['success' => true, 'data' => ['result' => $data, 'message' => 'success']], 200);
    }
}
