<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Enums\EDateFormat;
use App\Http\Repositories\AttendanceRepository;
use App\Http\Repositories\HolidayRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceServices
{
    private AttendanceRepository $attendanceRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->attendanceRepository = new AttendanceRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Get shift end time based on day of week
     * If today is Saturday and shift includes Saturday, use saturday_end_time
     * Otherwise use regular end time
     *
     * @param $shift
     * @param string|null $date Optional date to check (default: today)
     * @return string End time (H:i or H:i:s format)
     */
    private function getShiftEndTime($shift, $date = null)
    {
        $checkDate = $date ? Carbon::parse($date) : Carbon::now();
        $isSaturday = $checkDate->dayOfWeek === Carbon::SATURDAY; // 6 = Saturday

        // If shift includes Saturday and today is Saturday, use saturday_end_time
        if ($shift->includes_saturday && $isSaturday && $shift->saturday_end_time) {
            return substr($shift->saturday_end_time, 0, 5); // Return H:i format
        }

        // Otherwise use regular end time
        return $shift->end;
    }

    public function employeeAttendance($request)
    {
        $user_id = $request->user_id;
        $attendanceType = $request->attendanceType;
        $attendanceImage = $request->image ?? null;
        if ($request->hasFile('image')) {
            $attendanceImage = Helper::uploadFile(file: $request->image, file_folder_name: "attendance");
        }
        $_holidayRepository = new HolidayRepository();
        $_user = $this->userRepository->find($user_id);
        if (!$_user) {
            return Helper::errorResponseAPI(message: "Employee Not Found");
        }
        $_shift = $_user->getShift;

        $shift_type = $_shift->type;
        $is_early_check_in = $_shift->is_early_check_in;
        $is_early_check_out = $_shift->is_early_check_ou;
        $before_start = $_shift->before_start;
        $after_start = $_shift->after_start;
        $before_end = $_shift->before_end;
        $after_end = $_shift->after_end;


        $current_time = Helper::smTodayHis();
        $date = Helper::smTodayInYmd();
        $dateTime = Helper::smTodayInYmdHis();

        if ($shift_type == "night") {
            if ($attendanceType == "checkIn") {
                $shift_start = Carbon::createFromFormat('H:i', ($_shift->start));
                $holiday = $_holidayRepository->checkHolidayByDate($date);
                if ($holiday) {
                    return Helper::errorResponseAPI(message: "Check-In and Check-Out not allowed on holidays");
                }
                $_leave = $this->attendanceRepository->checkUserLeaveByDate($date, $user_id);
                if ($_leave) {
                    return Helper::errorResponseAPI(message: "Check-In and Check-Out not allowed on Leave");
                }
                $_attendance = $this->attendanceRepository->getUserAttendanceByDate(now()->format('Y-m-d'), $user_id);
                if ($_attendance) {
                    return Helper::errorResponseAPI(message: "Sorry ! employee cannot check in twice a day");
                }
                // Blocking logic removed - allow check-in at any time
                // Penalty calculation will be done after attendance save
                $_attendance = $this->attendanceRepository->save([
                    'user_id' => $_user->id,
                    'date' => $date,
                    'check_in' => $dateTime,
                    'check_in_image' => $attendanceImage,
                    'attendance_type' => Attendance::User,
                    'shift_id' => $_shift->id,
                ]);

                // Calculate delay and penalty after check-in (night shift)
                $checkInTime = Carbon::parse($dateTime);
                $shiftStartTime = Carbon::createFromFormat('H:i', $_shift->start);

                // Set shift date to same day as check-in
                $shiftStartTime->setDate(
                    $checkInTime->year,
                    $checkInTime->month,
                    $checkInTime->day
                );

                // Add grace period (after_start in minutes)
                $graceMinutes = $_shift->after_start ?? 0;
                $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

                $shiftWorkingHours = $_shift->working_hours ?? 8;

                $penaltyData = $this->calculateDelayAndPenalty(
                    $checkInTime,
                    $shiftStartTimeWithGrace,
                    $_user->monthly_salary,
                    $shiftWorkingHours
                );

                // Update attendance with penalty data
                $_attendance->update([
                    'delay_minutes' => $penaltyData['delay_minutes'],
                    'delay_penalty' => $penaltyData['penalty'],
                    'attendance_status' => $penaltyData['status'],
                    'daily_salary' => $penaltyData['daily_salary'],
                ]);

                $_return = [
                    'check_in' => date('H:i:s', strtotime($_attendance->check_in)),
                    'check_in_image' => ($_attendance->check_in_image && $_attendance->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendance->check_in_image) : "",
                    'check_out' => "-",
                    'check_out_image' => "",
                    'lunch_in' => "-",
                    'lunch_in_image' => "",
                    'lunch_out' => "-",
                    'lunch_out_image' => "",
                ];
                return Helper::successResponseAPI(message: "Success", data: $_return);
            }

            $st_time = strtotime($_shift->start);
            $end_time = strtotime($_shift->end);
            $cur_time = strtotime($current_time);

            if ($st_time < $cur_time && $cur_time < $end_time) {
                $_attendance = $this->attendanceRepository->getUserAttendanceByDate(now()->subDay()->format('Y-m-d'), $user_id);
                if ($attendanceType == "lunchIn") {

                    $lunch_in = $_attendance->lunch_in;
                    if ($lunch_in) {
                        return Helper::errorResponseAPI(message: "Already Check in for lunch");
                    }
                    $_attendance->update([
                        'lunch_in' => $dateTime,
                        'lunch_in_image' => $attendanceImage,
                    ]);
                    $_attendanceNew = Attendance::find($_attendance->id);
                    $_return = [
                        'check_in' => date('H:i:s', strtotime($_attendanceNew->check_in)),
                        'check_in_image' => ($_attendanceNew->check_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_in_image) : "",
                        'check_out' => "-",
                        'check_out_image' => "",
                        'lunch_in' => date('H:i:s', strtotime($_attendanceNew->lunch_in)),
                        'lunch_in_image' => ($_attendanceNew->lunch_in_image && $_attendanceNew->lunch_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_in_image) : "",
                        'lunch_out' => "-",
                        'lunch_out_image' => "",
                    ];
                    return Helper::successResponseAPI(message: "Success", data: $_return);
                }
                if ($attendanceType == "lunchOut") {

                    $lunch_out = $_attendance->lunch_out;
                    if ($lunch_out) {
                        return Helper::errorResponseAPI(message: "Already Check Out for lunch");
                    }
                    $_attendance->update([
                        'lunch_out' => $dateTime,
                        'lunch_out_image' => $attendanceImage,
                    ]);
                    $_attendanceNew = Attendance::find($_attendance->id);
                    $_return = [
                        'check_in' => date('H:i:s', strtotime($_attendanceNew->check_in)),
                        'check_in_image' => ($_attendanceNew->check_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_in_image) : "",
                        'check_out' => "-",
                        'check_out_image' => "",
                        'lunch_in' => date('H:i:s', strtotime($_attendanceNew->lunch_in)),
                        'lunch_in_image' => ($_attendanceNew->lunch_in_image && $_attendanceNew->lunch_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_in_image) : "",
                        'lunch_out' => date('H:i:s', strtotime($_attendanceNew->lunch_out)),
                        'lunch_out_image' => ($_attendanceNew->lunch_out_image && $_attendanceNew->lunch_out_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_out_image) : "",
                    ];
                    return Helper::successResponseAPI(message: "Success", data: $_return);

                }

                if ($attendanceType == "checkOut") {
                    $shift_end = Carbon::createFromFormat('H:i', ($_shift->end));
                    $check_out = $_attendance->check_out;
                    if ($check_out) {
                        return Helper::errorResponseAPI(message: "Already Check Out for Today");
                    }
                    // Blocking logic removed - allow check-out at any time

                    $startShiftTime = now()->subDay()->format('Y-m-d') . " " . $_shift->start . ":00";
                    $endShiftTime = $date . " " . $_shift->end . ":00";

                    $startShift = Carbon::createFromFormat('Y-m-d H:i:s', $startShiftTime);
                    $endShift = Carbon::createFromFormat('Y-m-d H:i:s', $endShiftTime);
                    $totalWorkingMinute = $endShift->diffInMinutes($startShift);

                    $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->check_in);
                    $totalWorkedInMinutes = now()->diffInMinutes($checkIn);

                    $lunchIn = Carbon::createFromFormat('Y-m-d H:i:s', ($_attendance->lunch_in ?? Helper::smStartOfDay()));
                    $lunchOut = Carbon::createFromFormat('Y-m-d H:i:s', ($_attendance->lunch_out ?? Helper::smStartOfDay()));
                    $totalLunchOutInMinute = $lunchOut->diffInMinutes($lunchIn);

                    // Préparer les données de mise à jour
                    $updateData = [
                        'check_out' => $dateTime,
                        'check_out_image' => $attendanceImage,
                        'total_working_duration' => $totalWorkedInMinutes,
                        'total_lunch_duration' => $totalLunchOutInMinute,
                        'total_over_time_duration' => ($totalWorkedInMinutes - $totalWorkingMinute),
                        'attendance_status' => 'on_time', // Marquer comme complet
                    ];

                    // Calcul du salaire pour les vacataires
                    if ($_user->employee_type === 'vacataire') {
                        $hoursWorked = ($totalWorkedInMinutes - $totalLunchOutInMinute) / 60;
                        $dailySalary = $hoursWorked * $_user->hourly_rate;
                        $updateData['hourly_rate'] = $_user->hourly_rate;
                        $updateData['daily_salary'] = round($dailySalary, 2);
                    }

                    $_attendance->update($updateData);
                    $_attendanceNew = Attendance::find($_attendance->id);
                    $_return = [
                        'check_in' => date('H:i:s', strtotime($_attendanceNew->check_in)),
                        'check_in_image' => ($_attendanceNew->check_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_in_image) : "",
                        'check_out' => date('H:i:s', strtotime($_attendanceNew->check_out)),
                        'check_out_image' => ($_attendanceNew->check_out_image && $_attendanceNew->check_out_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_out_image) : "",
                        'lunch_in' => date('H:i:s', strtotime($_attendanceNew->lunch_in)),
                        'lunch_in_image' => ($_attendanceNew->lunch_in_image && $_attendanceNew->lunch_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_in_image) : "",
                        'lunch_out' => date('H:i:s', strtotime($_attendanceNew->lunch_out)),
                        'lunch_out_image' => ($_attendanceNew->lunch_out_image && $_attendanceNew->lunch_out_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_out_image) : "",
                    ];
                    return Helper::successResponseAPI(message: "Success", data: $_return);
                }
            }
            return Helper::errorResponseAPI(message: "Employee not checked in yet");
        }


        $holiday = $_holidayRepository->checkHolidayByDate($date);
        if ($holiday) {
            return Helper::errorResponseAPI(message: "Check-In and Check-Out not allowed on holidays");
        }
        $_leave = $this->attendanceRepository->checkUserLeaveByDate($date, $user_id);
        if ($_leave) {
            return Helper::errorResponseAPI(message: "Check-In and Check-Out not allowed on Leave");
        }

        $_attendance = $this->attendanceRepository->getUserAttendanceByDate(now()->format('Y-m-d'), $user_id);
        if ($attendanceType == "checkIn") {
            if ($_attendance) {
                return Helper::errorResponseAPI(message: "Sorry ! employee cannot check in twice a day");
            }
            // Blocking logic removed - allow check-in at any time
            // Penalty calculation will be done after attendance save

            $_attendance = $this->attendanceRepository->save([
                'user_id' => $_user->id,
                'date' => $date,
                'check_in' => $dateTime,
                'check_in_image' => $attendanceImage,
                'attendance_type' => Attendance::User,
                'shift_id' => $_shift->id,
                'attendance_status' => 'incomplete', // En attente du check-out
            ]);

            // Calculate delay and penalty after check-in
            $checkInTime = Carbon::parse($dateTime);
            $shiftStartTime = Carbon::createFromFormat('H:i', $_shift->start);

            // Set shift date to same day as check-in
            $shiftStartTime->setDate(
                $checkInTime->year,
                $checkInTime->month,
                $checkInTime->day
            );

            // Add grace period (after_start in minutes)
            $graceMinutes = $_shift->after_start ?? 0;
            $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

            $shiftWorkingHours = $_shift->working_hours ?? 8;

            $penaltyData = $this->calculateDelayAndPenalty(
                $checkInTime,
                $shiftStartTimeWithGrace,
                $_user->monthly_salary,
                $shiftWorkingHours
            );

            // Update attendance with penalty data
            $_attendance->update([
                'delay_minutes' => $penaltyData['delay_minutes'],
                'delay_penalty' => $penaltyData['penalty'],
                'attendance_status' => $penaltyData['status'],
                'daily_salary' => $penaltyData['daily_salary'],
            ]);

            $_return = [
                'check_in' => date('H:i:s', strtotime($_attendance->check_in)),
                'check_in_image' => ($_attendance->check_in_image && $_attendance->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendance->check_in_image) : "",
                'check_out' => "-",
                'check_out_image' => "",
                'lunch_in' => "-",
                'lunch_in_image' => "",
                'lunch_out' => "-",
                'lunch_out_image' => "",
            ];
            return Helper::successResponseAPI(message: "Success", data: $_return);
        }
        if ($_attendance) {
            if ($attendanceType == "lunchIn") {
                $lunch_in = $_attendance->lunch_in;
                if ($lunch_in) {
                    return Helper::errorResponseAPI(message: "Already Check in for lunch");
                }
                $_attendance->update([
                    'lunch_in' => $dateTime,
                    'lunch_in_image' => $attendanceImage,
                ]);
                $_attendanceNew = Attendance::find($_attendance->id);
                $_return = [
                    'check_in' => date('H:i:s', strtotime($_attendanceNew->check_in)),
                    'check_in_image' => ($_attendanceNew->check_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_in_image) : "",
                    'check_out' => "-",
                    'check_out_image' => "",
                    'lunch_in' => date('H:i:s', strtotime($_attendanceNew->lunch_in)),
                    'lunch_in_image' => ($_attendanceNew->lunch_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_in_image) : "",
                    'lunch_out' => "-",
                    'lunch_out_image' => "",
                ];
                return Helper::successResponseAPI(message: "Success", data: $_return);
            }
            if ($attendanceType == "lunchOut") {
                $lunch_out = $_attendance->lunch_out;
                if ($lunch_out) {
                    return Helper::errorResponseAPI(message: "Already Check Out for lunch");
                }
                $_attendance->update([
                    'lunch_out' => $dateTime,
                    'lunch_out_image' => $attendanceImage,
                ]);
                $_attendanceNew = Attendance::find($_attendance->id);
                $_return = [
                    'check_in' => date('H:i:s', strtotime($_attendanceNew->check_in)),
                    'check_in_image' => ($_attendanceNew->check_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_in_image) : "",
                    'check_out' => "-",
                    'check_out_image' => "",
                    'lunch_in' => date('H:i:s', strtotime($_attendanceNew->lunch_in)),
                    'lunch_in_image' => ($_attendanceNew->lunch_in_image && $_attendanceNew->lunch_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_in_image) : "",
                    'lunch_out' => date('H:i:s', strtotime($_attendanceNew->lunch_out)),
                    'lunch_out_image' => ($_attendanceNew->lunch_out_image && $_attendanceNew->lunch_out_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_out_image) : "",
                ];
                return Helper::successResponseAPI(message: "Success", data: $_return);

            }
            if ($attendanceType == "checkOut") {
                $check_out = $_attendance->check_out;
                if ($check_out) {
                    return Helper::errorResponseAPI(message: "Already Check Out for Today");
                }
                // Blocking logic removed - allow check-out at any time

                $startShiftTime = $date . " " . $_shift->start . ":00";
                $endShiftTime = $date . " " . $_shift->end . ":00";

                $startShift = Carbon::createFromFormat('Y-m-d H:i:s', $startShiftTime);
                $endShift = Carbon::createFromFormat('Y-m-d H:i:s', $endShiftTime);
                $totalWorkingMinute = $endShift->diffInMinutes($startShift);

                $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->check_in);
                $totalWorkedInMinutes = now()->diffInMinutes($checkIn);


                $lunchIn = Carbon::createFromFormat('Y-m-d H:i:s', ($_attendance->lunch_in ?? Helper::smStartOfDay()));
                $lunchOut = Carbon::createFromFormat('Y-m-d H:i:s', ($_attendance->lunch_out ?? Helper::smStartOfDay()));
                $totalLunchOutInMinute = $lunchOut->diffInMinutes($lunchIn);

                // Préparer les données de mise à jour
                $updateData = [
                    'check_out' => $dateTime,
                    'check_out_image' => $attendanceImage,
                    'total_working_duration' => $totalWorkedInMinutes,
                    'total_lunch_duration' => $totalLunchOutInMinute,
                    'total_over_time_duration' => ($totalWorkedInMinutes - $totalWorkingMinute),
                    'attendance_status' => 'on_time', // Marquer comme complet
                ];

                // Calcul du salaire pour les vacataires
                if ($_user->employee_type === 'vacataire') {
                    $hoursWorked = ($totalWorkedInMinutes - $totalLunchOutInMinute) / 60;
                    $dailySalary = $hoursWorked * $_user->hourly_rate;
                    $updateData['hourly_rate'] = $_user->hourly_rate;
                    $updateData['daily_salary'] = round($dailySalary, 2);
                }

                $_attendance->update($updateData);
                $_attendanceNew = Attendance::find($_attendance->id);
                $_return = [
                    'check_in' => date('H:i:s', strtotime($_attendanceNew->check_in)),
                    'check_in_image' => ($_attendanceNew->check_in_image && $_attendanceNew->check_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_in_image) : "",
                    'check_out' => date('H:i:s', strtotime($_attendanceNew->check_out)),
                    'check_out_image' => ($_attendanceNew->check_out_image && $_attendanceNew->check_out_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->check_out_image) : "",
                    'lunch_in' => date('H:i:s', strtotime($_attendanceNew->lunch_in)),
                    'lunch_in_image' => ($_attendanceNew->lunch_in_image && $_attendanceNew->lunch_in_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_in_image) : "",
                    'lunch_out' => date('H:i:s', strtotime($_attendanceNew->lunch_out)),
                    'lunch_out_image' => ($_attendanceNew->lunch_out_image && $_attendanceNew->lunch_out_image != "-") ? asset('/uploads/attendance/' . $_attendanceNew->lunch_out_image) : "",
                ];
                return Helper::successResponseAPI(message: "Success", data: $_return);
            }
        }
        return Helper::errorResponseAPI(message: "Employee not checked in yet");
    }


    public function getTodayAttendanceApi(): AnonymousResourceCollection
    {
        $_attendance = $this->attendanceRepository->getTodayAttendanceList();
        return AttendanceResource::collection($_attendance);
    }

    public function getList($request)
    {
        $date = $request->date ?? null;
        return $this->attendanceRepository->getTodayAttendanceList($date);
    }

    public function getAttendance($attendance_id)
    {
        return $this->attendanceRepository->find($attendance_id);
    }


    /**
     * @throws SMException
     */
    public function attendanceUpdate($user_id, $attendanceType, $attendance_id = null , $attendance_date = null)
    {
        $_user = $this->userRepository->find($user_id);
        $_shift = $_user->getShift;

        if ($attendance_date){
            $date =Helper::smDate($attendance_date , EDateFormat::Ymd) ;
            $dateTime = $date ." ". Helper::smTodayHis();
        }else{
            $date = Helper::smTodayInYmd();
            $dateTime = Helper::smTodayInYmdHis();
        }


        if ($attendance_id) {
            $_attendance = $this->attendanceRepository->find($attendance_id);
        } else{
            $_attendance = $this->attendanceRepository->getUserAttendanceByDate($date, $user_id);
        }

        if ($attendanceType == "checkIn") {
            if ($_attendance) {
                if($_attendance->check_in){
                    throw new SMException("Sorry ! employee cannot check in twice a day");
                }
                $_attendance->update([
                    'check_in' => $dateTime,
                    'check_in_image' => '',
                ]);

                // Calculate delay and penalty after check-in (admin dashboard)
                $checkInTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
                $shiftStartTime = Carbon::createFromFormat('H:i', $_shift->start);

                // Set shift date to same day as check-in
                $shiftStartTime->setDate(
                    $checkInTime->year,
                    $checkInTime->month,
                    $checkInTime->day
                );

                // Add grace period (after_start in minutes)
                $graceMinutes = $_shift->after_start ?? 0;
                $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

                $shiftWorkingHours = $_shift->working_hours ?? 8;

                $penaltyData = $this->calculateDelayAndPenalty(
                    $checkInTime,
                    $shiftStartTimeWithGrace,
                    $_user->monthly_salary,
                    $shiftWorkingHours
                );

                // Update attendance with penalty data
                $_attendance->update([
                    'delay_minutes' => $penaltyData['delay_minutes'],
                    'delay_penalty' => $penaltyData['penalty'],
                    'attendance_status' => $penaltyData['status'],
                    'daily_salary' => $penaltyData['daily_salary'],
                ]);

                return $_attendance;
            }

            $_attendance = $this->attendanceRepository->save([
                'user_id' => $_user->id,
                'date' => $date,
                'check_in' => $dateTime,
                'check_in_image' => "",
                'attendance_type' => Attendance::Admin,
                'shift_id' => $_shift->id,
                'attendance_status' => 'incomplete', // En attente du check-out
            ]);

            // Calculate delay and penalty after check-in (admin dashboard)
            $checkInTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
            $shiftStartTime = Carbon::createFromFormat('H:i', $_shift->start);

            // Set shift date to same day as check-in
            $shiftStartTime->setDate(
                $checkInTime->year,
                $checkInTime->month,
                $checkInTime->day
            );

            // Add grace period (after_start in minutes)
            $graceMinutes = $_shift->after_start ?? 0;
            $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

            $shiftWorkingHours = $_shift->working_hours ?? 8;

            $penaltyData = $this->calculateDelayAndPenalty(
                $checkInTime,
                $shiftStartTimeWithGrace,
                $_user->monthly_salary,
                $shiftWorkingHours
            );

            // Update attendance with penalty data
            $_attendance->update([
                'delay_minutes' => $penaltyData['delay_minutes'],
                'delay_penalty' => $penaltyData['penalty'],
                'attendance_status' => $penaltyData['status'],
                'daily_salary' => $penaltyData['daily_salary'],
            ]);

            return $_attendance;
        }
        if ($_attendance) {
            if ($attendanceType == "lunchIn") {
                $lunch_in = $_attendance->lunch_in;
                if ($lunch_in) {
                    throw new SMException("Sorry ! Already Check in for lunch");
                }
                return $_attendance->update([
                    'lunch_in' => $dateTime,
                    'lunch_in_image' => "",
                ]);
            }
            if ($attendanceType == "lunchOut") {
                $lunch_out = $_attendance->lunch_out;
                if ($lunch_out) {
                    throw new SMException("Sorry ! Already Check Out for lunch");
                }
                return $_attendance->update([
                    'lunch_out' => $dateTime,
                    'lunch_out_image' => "",
                ]);

            }
            if ($attendanceType == "checkOut") {
                $check_out = $_attendance->check_out;
                if ($check_out) {
                    throw new SMException("Sorry ! Already Check Out for Today");
                }
                $startShiftTime = $date . " " . $_shift->start . ":00";
                $endShiftTime = $date . " " . $_shift->end . ":00";

                $startShift = Carbon::createFromFormat('Y-m-d H:i:s', $startShiftTime);
                $endShift = Carbon::createFromFormat('Y-m-d H:i:s', $endShiftTime);
                $totalWorkingMinute = $endShift->diffInMinutes($startShift);

                $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->check_in);
                $checkOut = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
                $totalWorkedInMinutes = $checkOut->diffInMinutes($checkIn);

                $lunchIn = Carbon::createFromFormat('Y-m-d H:i:s', ($_attendance->lunch_in ?? $dateTime));
                $lunchOut = Carbon::createFromFormat('Y-m-d H:i:s', ($_attendance->lunch_out ?? $dateTime));
                $totalLunchOutInMinute = $lunchOut->diffInMinutes($lunchIn);

                // Préparer les données de mise à jour
                $updateData = [
                    'check_out' => $dateTime,
                    'check_out_image' => "-",
                    'total_working_duration' => $totalWorkedInMinutes,
                    'total_lunch_duration' => $totalLunchOutInMinute,
                    'total_over_time_duration' => ($totalWorkedInMinutes - $totalWorkingMinute),
                    'attendance_status' => 'on_time', // Marquer comme complet
                ];

                // Calcul du salaire pour les vacataires
                if ($_user->employee_type === 'vacataire') {
                    $hoursWorked = ($totalWorkedInMinutes - $totalLunchOutInMinute) / 60;
                    $dailySalary = $hoursWorked * $_user->hourly_rate;
                    $updateData['hourly_rate'] = $_user->hourly_rate;
                    $updateData['daily_salary'] = round($dailySalary, 2);
                }

                return $_attendance->update($updateData);
            }
        }
        throw new SMException("Employee not checked in yet");
    }

    /**
     * @throws SMException
     */
    public function deleteAttendance($attendance_id)
    {
        $_attendance = $this->attendanceRepository->find($attendance_id);
        if ($_attendance) {
            return $this->attendanceRepository->delete($_attendance);
        }
        throw new SMException("Attendance Not found");
    }

    public function updateAttendanceDetail($request)
    {

        $attendance_id = $request->attendance_id;

        $request_check_in = $request->check_in;
        $request_check_out = $request->check_out;
        $request_lunch_in = $request->lunch_in;
        $request_lunch_out = $request->lunch_out;

        $_attendance = $this->attendanceRepository->find($attendance_id);

        $date = $_attendance->date;

        $_user = $this->userRepository->find($_attendance->user_id);
        $_shift = $_user->getShift;

        $startShiftTime = $date . " " . $_shift->start . ":00";
        $endShiftTime = $date . " " . $_shift->end . ":00";

        $startShift = Carbon::createFromFormat('Y-m-d H:i:s', $startShiftTime);
        $endShift = Carbon::createFromFormat('Y-m-d H:i:s', $endShiftTime);
        $totalWorkingMinute = $endShift->diffInMinutes($startShift);


        $checkIn = ($request_check_in) ? Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_check_in . ":00") : Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->check_in);

        $lunchIn = "";
        $lunchOut = "";
        $checkOut = "";
        $totalWorkedInMinutes = "";
        $totalLunchOutInMinute = "";
        $totalOverTimeDuration = "";
        if ($request_lunch_in) {
            $lunchIn = ($request_lunch_in) ? Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_lunch_in . ":00") : Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->lunch_in);
        }
        if ($request_lunch_out) {
            $lunchOut = ($request_lunch_out) ? Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_lunch_out . ":00") : Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->lunch_out);
            if ($request_lunch_in) {
                $totalLunchOutInMinute = $lunchOut->diffInMinutes($lunchIn);
            }
        }
        if ($request_check_out) {
            $checkOut = ($request_check_out) ? Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_check_out . ":00") : Carbon::createFromFormat('Y-m-d H:i:s', $_attendance->check_out);
            $totalWorkedInMinutes = $checkIn->diffInMinutes($checkOut);
            $totalOverTimeDuration = ($totalWorkedInMinutes - $totalWorkingMinute);
        }

        // Calculate delay and penalty (admin edit)
        $shiftStartTime = Carbon::createFromFormat('H:i', $_shift->start);
        $shiftStartTime->setDate($checkIn->year, $checkIn->month, $checkIn->day);

        // Add grace period (after_start in minutes)
        $graceMinutes = $_shift->after_start ?? 0;
        $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

        $shiftWorkingHours = $_shift->working_hours ?? 8;

        $updateData = [
            'check_in' => $checkIn->format("Y-m-d H:i:s"),
            'check_out' => ($checkOut) ? $checkOut->format("Y-m-d H:i:s") : null,
            'lunch_in' => ($lunchIn) ? $lunchIn->format("Y-m-d H:i:s") : null,
            'lunch_out' => ($lunchOut) ? $lunchOut->format("Y-m-d H:i:s") : null,
            'total_working_duration' => $totalWorkedInMinutes,
            'total_lunch_duration' => $totalLunchOutInMinute,
            'total_over_time_duration' => $totalOverTimeDuration,
        ];

        // Si c'est un vacataire, calculer le salaire horaire
        if ($_user->employee_type === 'vacataire' && $checkOut) {
            $hoursWorked = ($totalWorkedInMinutes - $totalLunchOutInMinute) / 60;
            $dailySalary = $hoursWorked * $_user->hourly_rate;

            $updateData['hourly_rate'] = $_user->hourly_rate;
            $updateData['daily_salary'] = round($dailySalary, 2);
        } else {
            // Permanent/Semi-permanent : calcul avec pénalités
            $penaltyData = $this->calculateDelayAndPenalty(
                $checkIn,
                $shiftStartTimeWithGrace,
                $_user->monthly_salary,
                $shiftWorkingHours
            );

            $updateData['delay_minutes'] = $penaltyData['delay_minutes'];
            $updateData['delay_penalty'] = $penaltyData['penalty'];
            $updateData['attendance_status'] = $penaltyData['status'];
            $updateData['daily_salary'] = $penaltyData['daily_salary'];
        }

        $_attendance->update($updateData);

    }

    /**
     * @throws SMException
     */
    public function saveAttendanceDetail($request)
    {
        $user_id = $request->user_id;
        $date = $request->date;
        $request_check_in = $request->check_in;
        $request_check_out = $request->check_out ?? null;
        $request_lunch_in = $request->lunch_in ?? null;
        $request_lunch_out = $request->lunch_out ?? null;

        $_user = $this->userRepository->find($user_id);
        $_attendance = $this->attendanceRepository->getUserAttendanceByDate($date, $user_id);
        if ($_attendance) {
            throw new SMException("Sorry ! Attendance has been already created for " . $_user->name . " on " . $date);
        }

        $_shift = $_user->getShift;

        $startShiftTime = $date . " " . $_shift->start . ":00";
        $endShiftTime = $date . " " . $_shift->end . ":00";
        $totalWorkedInMinutes = 0;
        $totalLunchOutInMinute = 0;
        $totalOverTimeDuration = 0;

        $lunchIn = null;
        $lunchOut = null;
        $startShift = Carbon::createFromFormat('Y-m-d H:i:s', $startShiftTime);
        $endShift = Carbon::createFromFormat('Y-m-d H:i:s', $endShiftTime);
        $totalWorkingMinute = $endShift->diffInMinutes($startShift);

        $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_check_in . ":00");
        if ($request_lunch_in) {
            $lunchIn = Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_lunch_in . ":00");
        }
        if ($request_lunch_out) {
            $lunchOut = Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_lunch_out . ":00");
            $totalLunchOutInMinute = ($request_lunch_in) ? $lunchOut->diffInMinutes($lunchIn) : 0;
        }
        if ($request_check_out) {
            $checkOut = Carbon::createFromFormat('Y-m-d H:i:s', $date . " " . $request_check_out . ":00");
            $totalWorkedInMinutes = $checkIn->diffInMinutes($checkOut);
            $totalOverTimeDuration = ($totalWorkedInMinutes - $totalWorkingMinute);
        }

        // Calculate delay and penalty (admin manual entry)
        $shiftStartTime = Carbon::createFromFormat('H:i', $_shift->start);
        $shiftStartTime->setDate($checkIn->year, $checkIn->month, $checkIn->day);

        // Add grace period (after_start in minutes)
        $graceMinutes = $_shift->after_start ?? 0;
        $shiftStartTimeWithGrace = $shiftStartTime->copy()->addMinutes($graceMinutes);

        $shiftWorkingHours = $_shift->working_hours ?? 8;

        $attendanceData = [
            'user_id' => $user_id,
            'date' => $date,
            'check_in' => $checkIn->format("Y-m-d H:i:s"),
            'check_out' => ($request_check_out) ? $checkOut->format("Y-m-d H:i:s") : $request_check_out,
            'lunch_in' => ($request_lunch_in) ? $lunchIn->format("Y-m-d H:i:s") : $request_lunch_in,
            'lunch_out' => ($request_lunch_out) ? $lunchOut->format("Y-m-d H:i:s") : $request_lunch_out,
            'total_working_duration' => $totalWorkedInMinutes,
            'total_lunch_duration' => $totalLunchOutInMinute,
            'total_over_time_duration' => $totalOverTimeDuration,
            'attendance_type' => Attendance::Admin,
        ];

        // Si c'est un vacataire, calculer le salaire horaire
        if ($_user->employee_type === 'vacataire' && $request_check_out) {
            $hoursWorked = ($totalWorkedInMinutes - $totalLunchOutInMinute) / 60;
            $dailySalary = $hoursWorked * $_user->hourly_rate;

            $attendanceData['hourly_rate'] = $_user->hourly_rate;
            $attendanceData['daily_salary'] = round($dailySalary, 2);
        } else {
            // Permanent/Semi-permanent : calcul avec pénalités
            $penaltyData = $this->calculateDelayAndPenalty(
                $checkIn,
                $shiftStartTimeWithGrace,
                $_user->monthly_salary,
                $shiftWorkingHours
            );

            $attendanceData['delay_minutes'] = $penaltyData['delay_minutes'];
            $attendanceData['delay_penalty'] = $penaltyData['penalty'];
            $attendanceData['attendance_status'] = $penaltyData['status'];
            $attendanceData['daily_salary'] = $penaltyData['daily_salary'];
        }

        $this->attendanceRepository->save($attendanceData);
    }

    /**
     * @throws SMException
     */
    public function monthlyAttendanceDetail($user_id, $year, $month)
    {
        $_user = $this->userRepository->find($user_id);
        $_shift = $_user->getShift;

        if ($year <= 2023 && $month < 12) {
            throw new SMException("Sorry ! Cannot get monthly attendance detail form Past 2023-12-01");
        }

        $_yearMonthDate = Carbon::createFromFormat('Y-m', $year . "-" . $month);
        $start = $_yearMonthDate->startOfMonth();
        $_yearMonthDate = Carbon::createFromFormat('Y-m', $year . "-" . $month);
        $end = $_yearMonthDate->endOfMonth();

        $total_days_of_month = $end->format('d');

        $startShiftTime = $_shift->start . ":00";
        $endShiftTime = $_shift->end . ":00";

        $startShift = Carbon::createFromFormat('H:i:s', $startShiftTime);
        $endShift = Carbon::createFromFormat('H:i:s', $endShiftTime);
        $totalWorkingHoursInDay = $endShift->diffInHours($startShift);


        $current_year = date('Y');
        $current_month = date('m');
        if($current_year == $year && $current_month == $month){
            $current_day = now()->format('d');
        }else{
            $current_day = $_yearMonthDate->format('d');
        }

        $totalWorkingHours = $totalWorkingHoursInDay * $total_days_of_month;
        $presentCount = 0;
        $absentCount = 0;
        $leaveCount = 0;
        $totalWorkedHours = 0;
        $attendanceDetail = [];

        $date_format = "d M Y (l)";

        if (strtotime($start->format('Y-m-d')) <= strtotime(now()->format('Y-m-d'))) {
            for ($i = 1; $i <= $current_day; $i++) {
                $temp_date = $year . "-" . $month . "-" . $i;
                $_temp_attendance = Attendance::whereDate('date', $temp_date)->where('user_id', $_user->id)->first();
                if ($_temp_attendance) {

                    $status = "absent";
                    if ($_temp_attendance->check_in) {
                        $status = "present ";
                        ++$presentCount;
                    }
                    if ($_temp_attendance->is_on_leave) {
                        ++$leaveCount;
                        $status = "leave";
                    }
                    if($status == "absent"){
                        ++$absentCount;
                    }
                    $totalWorkedHours += ($_temp_attendance->total_working_duration ?? 0);
                    $attendanceDetail[$i] = [
                        'attendance_id' => $_temp_attendance->id,
                        'user_id' => $_temp_attendance->user_id,
                        'date' => date($date_format, strtotime($_temp_attendance->date)),
                        'simple_date' => date("Y-m-d", strtotime($_temp_attendance->date)),
                        'check_in' => $_temp_attendance->check_in,
                        'check_out' => $_temp_attendance->check_out,
                        'lunch_in' => $_temp_attendance->lunch_in,
                        'lunch_out' => $_temp_attendance->lunch_out,
                        'check_in_image' => $_temp_attendance->check_in_image,
                        'check_out_image' => $_temp_attendance->check_out_image,
                        'lunch_in_image' => $_temp_attendance->lunch_in_image,
                        'lunch_out_image' => $_temp_attendance->lunch_out_image,
                        'is_on_leave' => $_temp_attendance->is_on_leave,
                        'attendance_type' => $_temp_attendance->attendance_type,
                        'status' => $status,
                    ];
                } else {
                    $_temp_attendance = $this->attendanceRepository->save([
                        'user_id' => $_user->id,
                        'date' => $temp_date,
                        'shift_id' => $_user->shift_id,
                        'attendance_type' => Attendance::Admin
                    ]);
                    $attendanceDetail[$i] = [
                        'attendance_id' => $_temp_attendance->id,
                        'user_id' => $_temp_attendance->user_id,
                        'date' => date($date_format, strtotime($_temp_attendance->date)),
                        'simple_date' => date("Y-m-d", strtotime($_temp_attendance->date)),
                        'check_in' => $_temp_attendance->check_in,
                        'check_out' => $_temp_attendance->check_out,
                        'lunch_in' => $_temp_attendance->lunch_in,
                        'lunch_out' => $_temp_attendance->lunch_out,
                        'check_in_image' => $_temp_attendance->check_in_image,
                        'check_out_image' => $_temp_attendance->check_out_image,
                        'lunch_in_image' => $_temp_attendance->lunch_in_image,
                        'lunch_out_image' => $_temp_attendance->lunch_out_image,

                        'is_on_leave' => $_temp_attendance->is_on_leave,
                        'attendance_type' => $_temp_attendance->attendance_type,
                        'status' => "absent"
                    ];
                    ++$absentCount;
                }
            }
        }
        return [
            $attendanceDetail,
            $absentCount,
            $presentCount,
            round(($totalWorkedHours / 60) , 2),
            $leaveCount,
            $totalWorkingHours,
            $total_days_of_month
        ];
    }

    /**
     * @throws SMException
     */
    public function attendanceDetailByDate($user_id, $year, $month)
    {
        $_user = $this->userRepository->find($user_id);
        if ($year <= 2023 && $month < 12) {
            throw new SMException("Sorry ! Cannot get monthly attendance detail");
        }

        $_yearMonthDate = Carbon::createFromFormat('Y-m', $year . "-" . $month);
        $start = $_yearMonthDate->startOfMonth();
        $current_day = now()->format('d');
        $attendanceDetail = [];
        $date_format = "d M Y (l)";

        if (strtotime($start->format('Y-m-d')) <= strtotime(now()->format('Y-m-d'))) {
            for ($i = 1; $i <= $current_day; $i++) {
                $temp_date = $year . "-" . $month . "-" . $i;
                $_temp_attendance = Attendance::whereDate('date', $temp_date)->where('user_id', $_user->id)->first();
                if ($_temp_attendance) {
                    $status = "absent";
                    if ($_temp_attendance->check_in) {
                        $status = "present ";
                    }
                    if ($_temp_attendance->is_on_leave) {
                        $status = "leave";
                    }
                    $attendanceDetail[$i] = [
                        'sn' => $i,
                        'name' => $_user->name,
                        'date' => date($date_format, strtotime($_temp_attendance->date)),
                        'check_in' => $_temp_attendance->check_in ?? "-",
                        'check_out' => $_temp_attendance->check_out ?? "-",
                        'lunch_in' => $_temp_attendance->lunch_in ?? "-",
                        'lunch_out' => $_temp_attendance->lunch_out ?? "-",
                        'is_on_leave' => ($_temp_attendance->is_on_leave)? "Yes": "-",
                        'attendance_type' => ucfirst($_temp_attendance->attendance_type),
                        'total_working_duration' => $_temp_attendance->total_working_duration ?? 0,
                        'total_lunch_duration' => $_temp_attendance->total_lunch_duration ?? 0,
                        'total_over_time_duration' => $_temp_attendance->total_over_time_duration ?? 0,
                        'status' => $status,
                    ];
                } else {
                    $attendanceDetail[$i] = [
                        'sn' => $i,
                        'name' => $_user->name,
                        'date' => date($date_format, strtotime($temp_date)),
                        'check_in' => "-",
                        'check_out' => "-",
                        'lunch_in' => "-",
                        'lunch_out' => "-",
                        'is_on_leave' => "-",
                        'attendance_type' => "-",
                        'total_working_duration' => 0,
                        'total_lunch_duration' => 0,
                        'total_over_time_duration' => 0,
                        'status' => "absent"
                    ];
                }
            }
        }
        return $attendanceDetail;

    }

    /**
     * Calculate delay in minutes and penalty amount based on monthly salary
     *
     * @param Carbon $checkInTime The actual check-in time
     * @param Carbon $shiftStartTime The expected shift start time
     * @param float|null $monthlySalary The employee's monthly salary
     * @param int $shiftWorkingHours Number of working hours per day (from shift)
     * @return array ['delay_minutes' => int, 'penalty' => float, 'status' => string, 'daily_salary' => float]
     */
    private function calculateDelayAndPenalty($checkInTime, $shiftStartTime, $monthlySalary, $shiftWorkingHours = 8)
    {
        $delayMinutes = 0;
        $penalty = 0;
        $status = 'on_time';
        $dailySalary = 0;

        // Si pas de salaire mensuel défini, pas de calcul de pénalité
        if (!$monthlySalary || $monthlySalary <= 0) {
            // Calculer quand même le retard en minutes
            if ($checkInTime->greaterThan($shiftStartTime)) {
                $delayMinutes = $checkInTime->diffInMinutes($shiftStartTime);
                $status = 'late';
            }
            return [
                'delay_minutes' => $delayMinutes,
                'penalty' => 0,
                'status' => $status,
                'daily_salary' => 0
            ];
        }

        // Calcul du salaire journalier (basé sur 30 jours)
        $dailySalary = $monthlySalary / 30;

        // Calcul du salaire horaire
        $hourlySalary = $dailySalary / $shiftWorkingHours;

        // Calcul du salaire par minute
        $perMinuteSalary = $hourlySalary / 60;

        // Vérifier si en retard
        if ($checkInTime->greaterThan($shiftStartTime)) {
            $delayMinutes = $checkInTime->diffInMinutes($shiftStartTime);
            $status = 'late';

            // Calculer la pénalité basée sur les minutes de retard
            $penalty = $delayMinutes * $perMinuteSalary;

            // Arrondir la pénalité à 2 décimales
            $penalty = round($penalty, 2);
        }

        return [
            'delay_minutes' => $delayMinutes,
            'penalty' => $penalty,
            'status' => $status,
            'daily_salary' => round($dailySalary - $penalty, 2) // Salaire du jour après déduction
        ];
    }
}
