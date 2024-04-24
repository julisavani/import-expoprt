<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminLogStored;
use App\Http\Controllers\Controller;
use App\Mail\OTP as MailOTP;
use App\Models\Admin;
use App\Models\Otp;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin', ['except' => ['login']]);
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Required field missing']], 422);
        }
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = Auth::guard('adminapi')->attempt($input)) {
            return response()->json(['success' => false, 'error' => ['message' => 'Something went wrong.', 'error_message' => 'Invalid Email or Password']], 401);
        }

        $user = Auth::guard('adminapi')->user();
        return response()->json(['success' => true, 'data' => ['result' => $user, 'token_type' => 'Bearer', 'token' => $jwt_token, 'message' => 'successfully.']], 200);
    }

    public function profile()
    {
        try {
            $User = Auth::guard('adminapi')->user();
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

        $User = Auth::guard('adminapi')->user();
        $User->name = $request->name;
        $User->email = $request->email;

        $data = $request->all();
        $data['log_entrytype'] = 1;
        $data['user_id'] = $User->id;
        $data['LogName'] = $User->name;
        $data['LName'] = 'Admin';
        $data['LogType'] = 1;
        $data['AdminType'] = 1;
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
        $user = Admin::Where('email', $request->email)->first();
        if ($user) {
            $data = $request->all();
            $data['otp'] = $otp;
            $otpObj = Otp::where('email', $request->email)->where('otptype', 1)->first();
            if ($otpObj) {
                $otpObj->update(['otp' => $otp]);
            } else {
                Otp::create(['email' => $request->email, 'otp' => $otp, 'otptype' => 1]);
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
        $otp = Otp::where('email', $request->email)->where('otptype', 1)->first();
        if ($otp->otp == $request->otp) {
            $otp->delete();
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
        $user = Admin::Where('email', $email)->first();
        if ($user) {
            $password = Hash::make($request->password);
            $user->update(['password' => $password]);
            return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'password change successfully.']], 200);
        }
        return response()->json(['success' => false, 'error' => ['message' => 'User not found']], 422);
    }

    public function logout()
    {
        Auth::guard('adminapi')->logout();
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
        $data = $request->all();
        $data['log_entrytype'] = 0;
        $data['user_id'] = $Profile->id;
        $data['LogName'] = $Profile->name;
        $data['LName'] = 'Admin Change Password';
        $data['LogType'] = 1;
        $data['AdminType'] = 1;
        $data['log_id'] = $Profile->id;
        event(new AdminLogStored($data));

        Admin::find($Profile->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['success' => true, 'data' => ['result' => [], 'message' => 'Password changed successfully!.']], 200);
    }
}
