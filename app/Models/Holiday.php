<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Holiday extends Model
{
    use HasFactory;

    protected $table = "holidays";
    protected $fillable = [
        'title',
        'date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->created_by = Auth::user()->id;
            $model->updated_by = Auth::user()->id;
        });

        static::updating(static function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }
}
