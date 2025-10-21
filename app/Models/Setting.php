<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    use HasFactory;
    protected $table = "settings";
    protected $fillable = [
        'title',
        'key',
        'value',
        'updated_by',
    ];
    public static function boot(): void
    {

        parent::boot();
        static::updating(static function ($model) {
            $model->updated_by = Auth::user()->id;
        });
//        if (!config('system.isDemo')){
//            Helper::checkUrl();
//        }
    }
}
