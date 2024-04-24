<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiamondHold extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'product_id', 'status'];

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
