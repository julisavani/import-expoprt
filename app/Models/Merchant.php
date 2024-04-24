<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'first_name', 'last_name', 'company', 'website', 'username', 'email', 'mobile', 'other_phone', 'about', 'job_title', 'business_type',
        'buying_group', 'group_title', 'address', 'city', 'state', 'pincode', 'country_id', 'status', 'verified_at','flag', 'token'
    ];

    public static $BusinessType = [
        1  => 'Diamond dealer/manufacturer/wholesaler',
        2  => 'Gemstone dealer/manufacturer/wholesaler',
        3  => 'Broker',
        4  => 'Jewelry designer',
        5  => 'Jewelry dealer/ manufacturer/wholesaler',
        6  => 'Retailer',
        7  => 'Private jeweler',
        8  => 'Appraiser',
        9  => 'Pawn shop owner',
        10 => 'Jewelry maker',
        11 => 'Press',
        12 => 'Organization affiliate',
        13 => 'Grading lab'
    ];

    public static $ByuingGroup = [
        1  => 'RJO',
        2  => 'CBG',
        3  => 'IJO',
        4  => 'SJO',
        5  => 'Other',
        6  => 'Not part of a buying group',
    ];
}
