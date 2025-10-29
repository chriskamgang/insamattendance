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
        // Champs vacataires
        'employee_type',
        'hourly_rate',
        'contract_start_date',
        'contract_end_date',
        'specialization',
        'contract_status',
        'max_hours_per_month',
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

    // Relations pour les vacataires
    public function vacataireContracts()
    {
        return $this->hasMany(VacataireContract::class, 'user_id');
    }

    public function activeContract()
    {
        return $this->hasOne(VacataireContract::class, 'user_id')
            ->where('status', 'active')
            ->latest();
    }

    public function monthlyPayments()
    {
        return $this->hasMany(VacataireMonthlyPayment::class, 'user_id');
    }

    // Vérifier si c'est un vacataire
    public function isVacataire()
    {
        return $this->employee_type === 'vacataire';
    }

    // Vérifier si c'est un permanent ou semi-permanent
    public function isPermanent()
    {
        return in_array($this->employee_type, ['permanent', 'semi_permanent']);
    }

}
