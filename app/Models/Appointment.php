<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date','time', 'remark', 'status'];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
