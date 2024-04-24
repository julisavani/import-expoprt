<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StarMelee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'shape', 'size', 'sieve', 'carat', 'def_vvs_vs', 'def_vs_si', 'fg_vvs_vs', 'fg_vs_si', 'pink_vvs_vs_si1', 'yellow_vvs_vs_si1', 'blue_vvs_vs_si1', 'status'
    ];
}
