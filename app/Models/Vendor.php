<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Vendor extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'company', 'website', 'username', 'email', 'email_verified_at', 'password', 'mobile', 'other_phone', 'about', 'job_title', 'business_type',
        'buying_group', 'group_title', 'address', 'city', 'state', 'pincode', 'country_id', 'status', 'verified_at','flag',
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
