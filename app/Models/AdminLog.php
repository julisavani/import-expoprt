<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;
    protected $fillable = ['admin_id','user_id','event','old_value','new_value','log_type','flag','log_id','product_id'];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static $LogType = [
        1   => 'Profile',
        2   => 'Users',
        3   => 'Clarity',
        4   => 'Color',
        5   => 'Size',
        6   => 'Shape',
        7   => 'Fancy Color',
        8   => 'Finish',
        9   => 'Fluorescence',
        10  => 'Policy',
        11  => 'Marketing',
        12  => 'Slot',
        13  => 'Request',
        14  => 'Hold',
        15  => 'Cart',
        16  => 'Confirm',
        17  => 'Inquiry',
        18  => 'Appointment',
        19  => 'Demand',
        20  => 'Diamond',
        21  => 'Product',
        22  => 'vendor',
        23  => 'merchant',
    ];
}
