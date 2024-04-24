<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marketing extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name','type','image','status'];

    public static $Lab = [
        1 => 'Home',
        2 => 'Dashboard'
    ];
}
