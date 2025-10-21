<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceIds extends Model
{
    use HasFactory;
    protected $table = "face_ids";
    protected $fillable = [
        'data',
        'user_id',
    ];
}
