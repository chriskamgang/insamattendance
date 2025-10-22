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
                if (!$is_early_check_in) {
                    $temp_shift_before_start = $shift_start->subMinutes($before_start)->format('H:i:s');
                    if (strtotime($current_time) < strtotime($temp_shift_before_start)) {
                        return Helper::errorResponseAPI(message: "Cannot Check In at moment. Please Check In after " . date('H:i',strtotime($temp_shift_before_start)));
                    }
                }
                $shift_start = Carbon::createFromFormat('H:i', ($_shift->start));
                $temp_shift_after_start = $shift_start->addMinutes($after_start);
                if (strtotime($current_time) > strtotime($temp_shift_after_start)) {
                    return Helper::errorResponseAPI(message: "Cannot Check In at moment. Check In Must Be done  " . date('H:i',strtotime($temp_shift_after_start)) . " Please Contact Admin");
                }
                $_attendance = $this->attendanceRepository->save([
                    'user_id' => $_user->id,
                    'date' => $date,
                    'check_in' => $dateTime,
                    'check_in_image' => $attendanceImage,
                    'attendance_type' => Attendance::User,
                    'shift_id' => $_shift->id,
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
                    if (!$is_early_check_out) {
                        $temp_shift_before_end = $shift_end->subMinutes($before_end)->format('H:i:s');
                        if (strtotime($current_time) < strtotime($temp_shift_before_end)) {
                            return Helper::errorResponseAPI(message: "Cannot Check In at moment. Please Check In after " . date('H:i',strtotime($temp_shift_before_end)));
                        }
                    }
                    $shift_end = Carbon::createFromFormat('H:i', ($_shift->end));
                    $temp_shift_after_end = $shift_end->addMinutes($after_end);
                    if (strtotime($current_time) > strtotime($temp_shift_after_end)) {
                        return Helper::errorResponseAPI(message: "Cannot Check In at moment. Check In Must Be done  " . date('H:i',strtotime($temp_shift_after_end)) . " Please Contact Admin");
                    }


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

                    $_attendance->update([
                        'check_out' => $dateTime,
                        'check_out_image' => $attendanceImage,
                        'total_working_duration' => $totalWorkedInMinutes,
                        'total_lunch_duration' => $totalLunchOutInMinute,
                        'total_over_time_duration' => ($totalWorkedInMinutes - $totalWorkingMinute),
                    ]);
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
            $shift_start = Carbon::createFromFormat('H:i', ($_shift->start));
            if (!$is_early_check_in) {
                $temp_shift_before_start = $shift_start->subMinutes($before_start)->format('H:i:s');
                if (strtotime($current_time) < strtotime($temp_shift_before_start)) {
                    return Helper::errorResponseAPI(message: "Cannot Check In at moment. Please Check In after " . date('H:i',strtotime($temp_shift_before_start)));
                }
            }
            $shift_start = Carbon::createFromFormat('H:i', ($_shift->start));
            $temp_shift_after_start = $shift_start->addMinutes($after_start);
            if (strtotime($current_time) > strtotime($temp_shift_after_start)) {
                return Helper::errorResponseAPI(message: "Cannot Check In at moment. Check In Must Be done  " . date('H:i',strtotime($temp_shift_after_start)) . " Please Contact Admin");
            }

            $_attendance = $this->attendanceRepository->save([
                'user_id' => $_user->id,
                'date' => $date,
                'check_in' => $dateTime,
                'check_in_image' => $attendanceImage,
                'attendance_type' => Attendance::User,
                'shift_id' => $_shift->id,
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
                $shift_end = Carbon::createFromFormat('H:i', ($_shift->end));
                $check_out = $_attendance->check_out;
                if ($check_out) {
                    return Helper::errorResponseAPI(message: "Already Check Out for Today");
                }
                if (!$is_early_check_out) {
                    $temp_shift_before_end = $shift_end->subMinutes($before_end)->format('H:i:s');
                    if (strtotime($current_time) < strtotime($temp_shift_before_end)) {
                        return Helper::errorResponseAPI(message: "Cannot Check Out at moment. Please Check Out after " . date('H:i',strtotime($temp_shift_before_end)));
                    }
                }
                $shift_end = Carbon::createFromFormat('H:i', ($_shift->end));
                $temp_shift_after_end = $shift_end->addMinutes($after_end);
                if (strtotime($current_time) > strtotime($temp_shift_after_end)) {
                    return Helper::errorResponseAPI(message: "Cannot Check Out at moment. Check Out Must Be done  " . date('H:i',strtotime($temp_shift_after_end)) . " Please Contact Admin");
                }

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
                $_attendance->update([
                    'check_out' => $dateTime,
                    'check_out_image' => $attendanceImage,
                    'total_working_duration' => $totalWorkedInMinutes,
                    'total_lunch_duration' => $totalLunchOutInMinute,
                    'total_over_time_duration' => ($totalWorkedInMinutes - $totalWorkingMinute),
                ]);
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
                return $_attendance->update([
                    'check_in' => $dateTime,
                    'check_in_image' => '',
                ]);
            }
            return $this->attendanceRepository->save([
                'user_id' => $_user->id,
                'date' => $date,
                'check_in' => $dateTime,
                'check_in_image' => "",
                'attendance_type' => Attendance::Admin,
                'shift_id' => $_shift->id,
            ]);
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

                return $_attendance->update([
                    'check_out' => $dateTime,
                    'check_out_image' => "-",
                    'total_working_duration' => $totalWorkedInMinutes,
                    'total_lunch_duration' => $totalLunchOutInMinute,
                    'total_over_time_duration' => ($totalWorkedInMinutes - $totalWorkingMinute),
                ]);
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
        $_attendance->update([
            'check_in' => $checkIn->format("Y-m-d H:i:s"),
            'check_out' => ($checkOut) ? $checkOut->format("Y-m-d H:i:s") : null,
            'lunch_in' => ($lunchIn) ? $lunchIn->format("Y-m-d H:i:s") : null,
            'lunch_out' => ($lunchOut) ? $lunchOut->format("Y-m-d H:i:s") : null,
            'total_working_duration' => $totalWorkedInMinutes,
            'total_lunch_duration' => $totalLunchOutInMinute,
            'total_over_time_duration' => $totalOverTimeDuration,
        ]);

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
        $this->attendanceRepository->save([
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
        ]);
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
}
