<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Http\Enums\EDateFormat;
use App\Http\Repositories\AttendanceRepository;
use App\Helper\Helper;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveServices
{
    private AttendanceRepository $leaveRepository;

    public function __construct()
    {
        $this->leaveRepository = new AttendanceRepository();
    }

    /**
     * @param $request
     * @throws SMException
     */
    public function saveLeave($request)
    {
        $user_id = $request->user_id;
        $from_date = Carbon::createFromFormat('Y-m-d', ($request->from_date ?? Helper::smStartOfDay()));
        $to_date = Carbon::createFromFormat('Y-m-d', ($request->to_date ?? Helper::smStartOfDay()));
        $no_of_days = $to_date->diffInDays($from_date);
        if($no_of_days == 0){
            $no_of_days = 1;
        }
        $leave_group_code = strtotime(now()->format(EDateFormat::Ymdhisa->value));
        $date = $from_date->format('Y-m-d');
        for ($i = 1 ; $i <= $no_of_days; $i++) {
            $_attendance = $this->leaveRepository->getUserAttendanceByDate($date, $user_id);
            if($_attendance){
                if($_attendance->check_in){
                    throw new SMException("Cannot apply for leave. Attendance is found ");
                }
                $this->leaveRepository->update($_attendance,[
                    'user_id' => $user_id,
                    'date' => $date,
                    'attendance_note' => $request->reason,
                    'total_working_duration' => 0,
                    'total_lunch_duration' => 0,
                    'total_over_time_duration' => 0,
                    'attendance_type' => "admin",
                    'is_on_leave' => true,
                    'leave_note' => $request->reason,
                    'leave_status' => "approve",
                    'leave_type_id' => $request->leave_type_id,
                    'leave_applied_by' => Auth::user()->id,
                    'leave_group_code' => $leave_group_code,
                ]);
            }else{
                $this->leaveRepository->save([
                    'user_id' => $user_id,
                    'date' => $date,
                    'attendance_note' => $request->reason,
                    'total_working_duration' => 0,
                    'total_lunch_duration' => 0,
                    'total_over_time_duration' => 0,
                    'attendance_type' => "admin",
                    'is_on_leave' => true,
                    'leave_note' => $request->reason,
                    'leave_status' => "approve",
                    'leave_type_id' => $request->leave_type_id,
                    'leave_applied_by' => Auth::user()->id,
                    'leave_group_code' => $leave_group_code,
                ]);
            }
            $new_date = Carbon::createFromFormat('Y-m-d', ($date));
            $temp_date = $new_date->addDay();
            $date = $temp_date->format('Y-m-d');
        }
    }

    public function leaveList()
    {
        $_leaves = [];
        $_attendances = $this->leaveRepository->getDistinctLeaveGroupCode();
        foreach ($_attendances as $attendance) {
            $temp_attendance = $this->leaveRepository->getLeaveByLeaveGroupCode($attendance->leave_group_code);
            $_leaves[] = $temp_attendance;
        }
        return $_leaves;
    }

    public function leaveEdit($leave_group_code)
    {
        return $this->leaveRepository->getLeaveByLeaveGroupCode($leave_group_code);
    }

    /**
     * @throws SMException
     */
    public function updateLeave($request , $leave_group_code)
    {
        $saved_leaves = Attendance::where('leave_group_code', $leave_group_code)->get();
        foreach ($saved_leaves as $saved_leaf)
        {
            $this->leaveRepository->delete($saved_leaf);
        }
        $this->saveLeave($request);
    }

    public function deleteLeave($leave_group_code)
    {
        $saved_leaves = Attendance::where('leave_group_code', $leave_group_code)->get();
        foreach ($saved_leaves as $saved_leaf)
        {
            $this->leaveRepository->delete($saved_leaf);
        }
    }
}
