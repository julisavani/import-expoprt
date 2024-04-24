<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\StarMeleeMail;
use App\Models\StarMelee;
use App\Models\StarMeleeInquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StarMeleeController extends Controller
{
    public function shape()
    {
        $Shape = StarMelee::select('shape')->distinct('shape')->where('shape','<>', 'Round')->get();
        return response()->json(['success' => true, 'data' => ['result' => $Shape, 'message' => 'successfully.']], 200);
    }

    public function carat(Request $request)
    {
        $shape = $request->shape;
        $color = $request->color;
        $clarity = $request->clarity;
        $columns = $color .'_'.$clarity;
        $carat = StarMelee::select('size','sieve','carat')->where('shape', $shape)->whereNotNull($columns)->get();
        return response()->json(['success' => true, 'data' => ['result' => $carat, 'message' => 'successfully.']], 200);
    }
    public function price(Request $request)
    {
        $shape = $request->shape;
        $color = $request->color;
        $clarity = $request->clarity;
        $columns = $color .'_'.$clarity;
        $carat = $request->carat;
        $carat = StarMelee::select($columns .' as price')->where('shape', $shape)->where('carat', $carat)->get();
        return response()->json(['success' => true, 'data' => ['result' => $carat, 'message' => 'successfully.']], 200);
    }

    public function inquiry(Request $request)
    {
        $data['shape'] = $request->shape;
        $data['color'] = $request->color;
        $data['clarity'] = $request->clarity;
        $data['carat'] = $request->carat;
        $data['price'] = $request->price;
        $data['qty'] = $request->qty;
        if($request->has('name')){
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['mobile'] = $request->mobile;
        }
        if($request->has('user_id')){
            $data['user_id'] = $request->user_id;
            $user = User::find($request->user_id);
            $data['name'] = $user->username;
            $data['email'] = $user->email;
            $data['mobile'] = $user->mobile;

        }
        $Inquiry = StarMeleeInquiry::create($data);
        $details['subject'] = 'New inquiry for Star Melee on Delight Diamond';
        $details['data'] = $data;
        Mail::to('sales@delightdiamonds.com')->send(new StarMeleeMail($details));
        return response()->json(['success' => true, 'data' => ['result' => $Inquiry, 'message' => 'successfully.']], 200);
    }

    public function inquirylist()
    {
        $User = auth()->guard('userapi')->user();
        $inquiry = StarMeleeInquiry::where('user_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $inquiry, 'message' => 'successfully.']], 200);
    }
}
