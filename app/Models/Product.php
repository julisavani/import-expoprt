<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id', 'stone_id', 'cert_no', 'cert_type', 'cert_url', 'image', 'video', 'diamond_type', 'size_id', 'carat', 'shape_id', 'color_id', 'colors_id', 'overtone_id', 'intensity_id',
        'clarity_id', 'cut_id', 'polish_id', 'symmetry_id', 'fluorescence_id', 'rapo_rate', 'discount', 'rate', 'amount', 'table', 'table_per', 'depth', 'depth_per', 'measurement',
        'length', 'width', 'height', 'ratio', 'bgm_id', 'city', 'country', 'status', 'rapo_amount','hold', 'confirm',
        'availability', 'milky', 'shade', 'crown_angle', 'crown_height', 'crown_open', 'pavilion_angle', 'pavilion_height', 'pavilion_open', 'white_table', 'white_side', 'table_black',
        'side_black', 'table_open', 'girdle', 'girdle_desc', 'culet', 'key_to_symbols',
        'fluorescence_color_id', 'pair', 'h_a', 'eye_clean', 'growth_type', 'vendor_id', 'merchant_id','treatment','inscription','comment'
    ];

    public static $Type = [
        1 => 'HPHT',
        2 => 'CVD',
        3 => 'Other'
    ];

    public static $DiamondType = [
        1 => 'Natural Diamond',
        2 => 'Lab-Grown Diamond'
    ];

    public static $Lab = [
        0 => '',
        1 => 'IGI',
        2 => 'GIA',
        3 => 'HRD',
        4 => 'Delight Grading'
    ];

    protected $appends = [
        "size_name",
        "shape_name",
        "color_name",
        "colors_name",
        "overtone_name",
        "intensity_name",
        "clarity_name",
        "cut_name",
        "polish_name",
        "symmetry_name",
        "fluorescence_name",
        "fluorescence_color_name",
        "bgm_name",
        'diamond_type_name',
        'cert'
    ];

    public function getSizeNameAttribute()
    {
        $Size = Size::find($this->size_id);
        return $Size->name ?? '';
    }
    public function getShapeNameAttribute()
    {
        $Shape = Shape::find($this->shape_id);
        return $Shape->name ?? '';
    }
    public function getColorNameAttribute()
    {
        $Color = Color::find($this->color_id);
        return $Color->name ?? '';
    }
    public function getColorsNameAttribute()
    {
        $Colors = FancyColor::find($this->colors_id);
        return $Colors->name ?? '';
    }
    public function getOvertoneNameAttribute()
    {
        $overtone = FancyColor::find($this->overtone_id);
        return $overtone->name ?? '';
    }
    public function getIntensityNameAttribute()
    {
        $intensity = FancyColor::find($this->intensity_id);
        return $intensity->name ?? '';
    }
    public function getClarityNameAttribute()
    {
        $Clarity = Clarity::find($this->clarity_id);
        return $Clarity->name ?? '';
    }

    public function getCutNameAttribute()
    {
        $Cut = Finish::find($this->cut_id);
        return $Cut->name ?? '';
    }
    public function getPolishNameAttribute()
    {
        $Polish = Finish::find($this->polish_id);
        return $Polish->name ?? '';
    }
    public function getSymmetryNameAttribute()
    {
        $Symmetry = Finish::find($this->symmetry_id);
        return $Symmetry->name ?? '';
    }
    public function getFluorescenceNameAttribute()
    {
        $Fluorescence = Fluorescence::find($this->fluorescence_id);
        return $Fluorescence->name ?? '';
    }
    public function getFluorescenceColorNameAttribute()
    {
        $Fluorescence = Fluorescence::find($this->fluorescence_color_id);
        return $Fluorescence->name ?? '';
    }
    public function getBgmNameAttribute()
    {
        $Bgm = Fluorescence::find($this->bgm_id);
        return $Bgm->name ?? '';
    }
    public function getDiamondTypeNameAttribute()
    {
        return Product::$Type[$this->diamond_type] ?? '';
    }
    public function getCertAttribute()
    {
        return Product::$Lab[$this->cert_type] ?? '';
    }
}
