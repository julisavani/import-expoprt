<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marketing;
use App\Models\Product;
use App\Models\SaveSearch;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $User = Auth::guard('userapi')->user();
        $result['NewArrival'] = Product::where('status', 1)->where("created_at",">",Carbon::now()->subDay())->count();
        $result['PairStone'] = Product::where('status', 1)->where('confirm', 0)->where('pair', '!=', 'No')->where('pair', '!=', '')->whereNotNull('pair')->count();
        $result['OfferAtYou'] = 0;
        $result['SaveSearch'] = SaveSearch::where('user_id', $User->id)->count();
        $Setting = Setting::first();
        $result['DiscountMeeter'] = $Setting->discount ?? 0;
        $result['Marketing'] = Marketing::where('type', 2)->get();
        $result['SearchList'] = SaveSearch::where('user_id', $User->id)->get();
        return response()->json(['success' => true, 'data' => ['result' => $result, 'message' => 'successfully.']], 200);
    }

    public function marketing()
    {
        $Marketing = Marketing::where('type', 1)->get();
        return response()->json(['success' => true, 'data' => ['result' => $Marketing, 'message' => 'successfully.']], 200);
    }

    public function pair()
    {
        $Pair = Product::where('status', 1)->where('confirm', 0)->where('pair', '!=', 'No')->where('pair', '!=', '')->whereNotNull('pair')->get();
        return response()->json(['success' => true, 'data' => ['result' => $Pair, 'message' => 'successfully.']], 200);
    }


}
