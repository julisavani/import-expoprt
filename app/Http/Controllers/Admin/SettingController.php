<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function dashboard()
    {
        $TotalStockAmount = Product::where('confirm', 0)->sum('amount');
        $result['TotalStockAmount'] = round($TotalStockAmount,2);
        $result['TotalStockCount'] = Product::where('confirm', 0)->count();
        $result['Salecount'] = Product::where('confirm', 1)->sum('amount');
        $SaleAmount = Product::where('confirm', 1)->sum('amount');
        $result['SaleAmount'] = round($SaleAmount, 2);
        $result['Customer'] = User::where('status', 1)->count();
        return response()->json(['success' => true, 'data' => ['result' => $result, 'message' => 'successfully.']], 200);
    }
}
