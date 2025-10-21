<?php

namespace App\Http\Repositories;

use App\Helper\Helper;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class AttendanceRepository
{


    private Attendance $attendance;

    public function __construct()
    {
        $this->attendance = new Attendance();
    }

    public function getTodayAttendanceList($date_filter = null): mixed
    {
        return User::select([
            DB::raw('users.id as user_id'),
            DB::raw('users.name as name'),
            DB::raw('attendances.date as date'),
            DB::raw('attendances.check_in as check_in'),
            DB::raw('attendances.check_out as check_out'),
            DB::raw('attendances.lunch_in as lunch_in'),
            DB::raw('attendances.lunch_out as lunch_out'),
            DB::raw('attendances.check_in_image as check_in_image'),
            DB::raw('attendances.check_out_image as check_out_image'),
            DB::raw('attendances.lunch_in_image as lunch_in_image'),
            DB::raw('attendances.lunch_out_image as lunch_out_image'),
            DB::raw('attendances.is_on_leave as is_on_leave'),
            DB::raw('attendances.id as attendance_id'),
            DB::raw('attendances.attendance_type as attendance_type'),
            DB::raw('shifts.type as shift_type'),
        ])->leftJoin('attendances', function ($join) use ($date_filter) {
            $join->on('users.id', '=', 'attendances.user_id');
            if($date_filter){
                $join->whereDate('attendances.date', '<=', ($date_filter ?? Helper::smTodayInYmd()));
                $join->whereDate('attendances.date', '>=', ($date_filter ?? Helper::smTodayInYmd()));
            }else{
                $join->where('attendances.date', Helper::smTodayInYmd());
            }
        })->leftJoin('shifts', function ($join) {
            $join->on('shifts.id', '=', 'users.shift_id');
        })->where('user_type', 'employee')

            ->groupBy('users.id')
            ->orderBy('users.id', 'asc')
            ->get();
    }

    public function find($id): mixed
    {
        return $this->attendance->find($id);
    }


    public function getUserAttendanceByDate($date, $user_id)
    {
        return $this->attendance->where('user_id', $user_id)->whereDate('date', $date)->first();
    }

    public function getUserAttendanceNightByDate($date, $user_id)
    {
        return $this->attendance->whereNull('check_in')->where('user_id', $user_id)->whereDate('date', $date)->first();
    }

    public function save($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return $this->attendance->create($data)->fresh();
        });
    }

    public function update($attendance, $data): mixed
    {
        return DB::transaction(static function () use ($attendance, $data) {
            return $attendance->update($data);
        });
    }

    public function checkUserLeaveByDate($date, $user_id)
    {
        return $this->attendance->whereDate('date', $date)
            ->where('user_id', $user_id)
            ->where('is_on_leave', 1)
            ->first();
    }

    public function getDistinctLeaveGroupCode()
    {
        return $this->attendance->distinct()->select('leave_group_code')->whereNotNull('leave_group_code')->get();
    }

    public function getLeaveByLeaveGroupCode($leave_group_code)
    {
        return $this->attendance->join('users as employee', 'employee.id', '=', 'attendances.user_id')
            ->join('users as admin', 'admin.id', '=', 'attendances.leave_applied_by')
            ->join('leave_types', 'leave_types.id', '=', 'attendances.leave_type_id')
            ->select([
                DB::raw('count(attendances.id) as no_of_days'),
                DB::raw('MIN(attendances.date) as from_date'),
                DB::raw('MAX(attendances.date) as to_date'),
                DB::raw('employee.name as user_name'),
                DB::raw('employee.id as user_id'),
                DB::raw('admin.name as applied_by'),
                DB::raw('leave_types.title as leave_type_title'),
                DB::raw('leave_types.id as leave_type_id'),
                DB::raw('attendances.leave_status as leave_status'),
                DB::raw('attendances.leave_note as leave_note'),
                DB::raw('attendances.leave_group_code as leave_group_code'),
            ])->where('leave_group_code', $leave_group_code)
            ->orderBy('attendances.date', 'asc')
            ->first();
    }

    public function delete(Attendance $attendance): mixed
    {
        return DB::transaction(static function () use ($attendance) {
            return $attendance->delete();
        });
    }

    public function checkAttendanceFromDate($form_date, $to_date)
    {
        return $this->attendance->whereDate('date', '>=', $form_date)->whereDate('date', '<=', $to_date)->first();
    }


}
