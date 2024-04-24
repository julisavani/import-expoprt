<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid', 'stone_id', 'cert_no', 'cert_type', 'cert_url', 'image', 'video', 'diamond_type', 'size_id', 'carat', 'shape_id', 'color_id', 'colors_id', 'overtone_id', 'intensity_id',
        'clarity_id', 'cut_id', 'polish_id', 'symmetry_id', 'fluorescence_id', 'rapo_rate', 'discount', 'rate', 'amount', 'table', 'table_per', 'depth', 'depth_per', 'measurement',
        'length', 'width', 'height', 'ratio', 'bgm_id', 'city', 'country', 'status', 'rapo_amount',
        'availability', 'milky', 'shade', 'crown_angle', 'crown_height', 'crown_open', 'pavilion_angle', 'pavilion_height', 'pavilion_open', 'white_table', 'white_side', 'table_black',
        'side_black', 'table_open', 'girdle', 'girdle_desc', 'culet', 'key_to_symbols',
        'fluorescence_color_id', 'pair', 'h_a', 'eye_clean', 'growth_type','import_type','imported_at','vendor_id','merchant_id','treatment','inscription','comment'
    ];
}
