<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name','type','description','version','status'];

    public static $Lab = [
        1 => 'Terms & Conditions',
        2 => 'Privacy Policy'
    ];
}
