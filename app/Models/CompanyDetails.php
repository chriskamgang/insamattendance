<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyDetails extends Model
{
    use HasFactory;

    protected $table = "company_details";
    protected $fillable = [
        'name',
        'primary_email',
        'secondary_email',
        'primary_contact_no',
        'secondary_contact_no',
        'address',
        'image',
        'website_url',
        'updated_by',
    ];
    protected $appends = ['image_path'];

    /**
     * @return string
     */
    public function getImagePathAttribute(): string
    {
        if( $this->image){
            $path = asset('/uploads/company');
            return $path . '/' . $this->image;
        }
        return "";

    }

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();
        static::updating(static function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }
}
