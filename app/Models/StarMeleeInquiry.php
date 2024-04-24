<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StarMeleeInquiry extends Model
{
    use HasFactory;
    protected $fillable = [
        'shape', 'color', 'clarity', 'carat','price', 'qty', 'name', 'email', 'mobile', 'user_id', 'status'
    ];
}
