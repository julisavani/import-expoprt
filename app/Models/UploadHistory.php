<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_name', 'upload_type', 'status', 'total', 'valid', 'invalid', 'uuid','vendor_id'
    ];
}
