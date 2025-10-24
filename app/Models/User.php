<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes;

    protected $table = "users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'dob',
        'email',
        'mobile',
        'address',
        'image',
        'password',
        'app_password',
        'user_type',
        'face_id',
        'department_id',
        'shift_id',
        'monthly_salary',
        'two_factor_code',
        'two_factor_expires_at',
        'otp_verify_status',
        'unique_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];
    const userType = ['admin','employee'];

    public function getShift()
    {
        return $this->belongsTo(Shift::class,'shift_id','id');
    }
    public function getDepartment()
    {
        return $this->belongsTo(Department::class,'department_id','id');
    }

    public function countFaceIds()
    {
        return FaceIds::where("user_id" , $this->id)->count();
    }

}
