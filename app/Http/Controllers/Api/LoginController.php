<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\OTP as MailOTP;
use App\Mail\RegisterMail;
use App\Models\Country;
use App\Models\Otp;
use App\Models\Policy;
use App\Models\User;
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
        $this->middleware('apiAuth', ['except' => ['login','register','country','forgotpassword','verifyotp','updatepassword','activation']]);
    }

    public function register(Request $request)
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
        $Users = User::create($data);

        // $input = $request->only('email', 'password');
        // $input['status'] = 1;
        // $jwt_token = null;
        $hashid = Crypt::encryptString($Users->id);

        $details['subject'] = 'Confirm your email address to get started on Delight Diamond';
        $details['email'] = $request->email;
        $details['url'] = 'https://delightdiamonds.com/activation/' .$hashid;
        Mail::to($request->email)->send(new RegisterMail($details));

        // if (!$jwt_token = Auth::guard('userapi')->attempt($input)) {
        //     return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid Email or Password']], 401);
        // }

        // $user = Auth::guard('userapi')->user();
        return response()->json(['success' => true, 'data' => ['result' => $Users, 'message' => 'successfully.']], 200);
    }

    public function activation($id)
    {
        $hashid1 = Crypt::decryptString($id);
        $User = User::find($hashid1);
        if($User) {
            User::find($hashid1)->update([
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
        $input['user_type'] = 1;
        if(filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $input['email'] = $request->email;
        } else {
            $input['username'] = $request->email;
        }
        // dd($input);

        if (!$jwt_token = Auth::guard('userapi')->attempt($input)) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid Email/Username or Password']], 401);
        }

        $user = Auth::guard('userapi')->user();
        return response()->json(['success' => true, 'data' => ['result' => $user, 'token_type' => 'Bearer', 'token' => $jwt_token, 'message' => 'successfully.']], 200);
    }

    public function profile()
    {
        try {
            $User = Auth::guard('userapi')->user();
            return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'successfully.']], 200);
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

        $User = Auth::guard('userapi')->user();

        $User->username = $request->username;
        $User->email = $request->email;
        $User->fullname = $request->fullname;
        $User->country_id = $request->country_id;
        $User->company = $request->company;
        $User->mobile = $request->mobile;
        $User->whatsapp = $request->whatsapp;
        $User->wechat = $request->wechat;
        $User->skype = $request->skype;

        if($request->hasFile('document')){
            $file = $request->file('document');
            $filename = str_replace(' ', '', $request->username.'-'.date('ymdHis').'.'.$file->getClientOriginalExtension());
            // unlink(storage_path('app/public/document/'.$User->document));
            $file->storeAs('public/document', $filename);
            $User->document = $filename;
        }
        $data = $request->all();
        $data['log_entrytype'] = 1;
        $data['user_id'] = $User->id;
        $data['LogName'] = $User->username;
        $data['LName'] = 'User';
        $data['LogType'] = 1;
        $data['AdminType'] = 0;
        $data['log_id'] = $User->id;
        event(new AdminLogStored($data));
        $User->save();
        return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'successfully.']], 200);
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
        $user = User::Where('email', $request->email)->first();
        if ($user) {
            $data = $request->all();
            $data['otp'] = $otp;
            $otpObj = Otp::where('email', $request->email)->where('otptype', 2)->first();
            if ($otpObj) {
                $otpObj->update(['otp' => $otp]);
            } else {
                Otp::create(['email' => $request->email, 'otp' => $otp, 'otptype' => 2]);
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

        $otp = Otp::where('email', $request->email)->where('otptype', 2)->first();
        if($otp){
            if ($otp->otp == $request->otp) {
                $otp->delete();
                return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'otp verifid successfully.']], 200);
            }
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid OTP']], 422);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'User not found']], 422);
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
        $user = User::Where('email', $email)->first();
        if ($user) {
            $password = Hash::make($request->password);
            $user->update(['password' => $password]);
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'password change successfully.']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'User not found']], 422);
    }

    public function logout()
    {
        Auth::guard('userapi')->logout();
        return response()->json(['success' => true, 'data' => ['message' => 'Log out successfully.']], 200);
    }

    public function changepassword(Request $request)
    {
        $Profile = Auth::guard('userapi')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        #Match The Old Password
        if(!Hash::check($request->old_password, $Profile->password)){
            return response()->json(['success' => false, 'error' => ['message' => "Old Password Doesn't match!."]], 422);
        }

        #Update the new Password
        User::find($Profile->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $data = $request->all();
        $data['log_entrytype'] = 0;
        $data['user_id'] = $Profile->id;
        $data['LogName'] = $Profile->username;
        $data['LName'] = 'User Change Password';
        $data['LogType'] = 1;
        $data['AdminType'] = 0;
        $data['log_id'] = $Profile->id;
        event(new AdminLogStored($data));

        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Password changed successfully!.']], 200);
    }

    public function country()
    {
        $Country = Country::all();
        return response()->json(['success' => true, 'data' => ['result' => $Country, 'message' => 'success']], 200);
    }

    public function terms(Request $request)
    {
        $Profile = Auth::guard('userapi')->user();

        #Update the new Password
        User::find($Profile->id)->update([
            'terms' => 1,
            'version' => $request->version
        ]);

        $data = $request->all();
        $data['log_entrytype'] = 0;
        $data['user_id'] = $Profile->id;
        $data['LogName'] = $Profile->username;
        $data['LName'] = 'User Accept Terms Version : '. $request->version;
        $data['LogType'] = 1;
        $data['AdminType'] = 0;
        $data['log_id'] = $Profile->id;
        event(new AdminLogStored($data));

        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Terms Accept successfully!.']], 200);
    }

    public function policy()
    {
        $Policy = Policy::where('type',1)->where('status',1)->first();
        return response()->json(['success' => true, 'data' => ['result' => $Policy, 'message' => 'successfully!.']], 200);
    }

    public function changediamond(Request $request)
    {
        $User = Auth::guard('userapi')->user();
        $User->diamond_type = $request->diamond_type;
        $User->save();
        return response()->json(['success' => true, 'data' => ['result' => $User, 'message' => 'successfully.']], 200);
    }
}
