<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = "attendances";
    protected $fillable = [
        'user_id',
        'date',

        'check_in',
        'check_out',
        'lunch_in',
        'lunch_out',

        'check_in_image',
        'check_out_image',
        'lunch_in_image',
        'lunch_out_image',

        'attendance_note',
        'total_working_duration',
        'total_lunch_duration',
        'total_over_time_duration',
        'attendance_type',

        'is_on_leave',
        'leave_note',
        'leave_status',
        'leave_type_id',
        'leave_applied_by',
        'leave_group_code',

        'shift_id',
        'updated_by',
    ];

    public const User = 'user';
    public const Admin = 'admin';

    public function getUser()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function getUsLeaveType()
    {
        return $this->belongsTo(LeaveTypes::class,'leave_type_id','id');
    }
}
