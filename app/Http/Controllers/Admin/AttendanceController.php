<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\SettingRepository;
use App\Http\Requests\AttendanceCreateRequest;
use App\Http\Requests\AttendanceRequest;
use App\Http\Services\AttendanceServices;
use App\Http\Services\UserServices;
use App\Models\Attendance;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    private string $basePath = "attendance.";
    private string $routePath = "admin.attendance.";
    private string $error_message = "Oops! Something went wrong.";
    private AttendanceServices $attendanceServices;
    private UserServices $userServices;
    private SettingRepository $settingRepository;

    public function __construct()
    {
        $this->attendanceServices = new AttendanceServices();
        $this->userServices = new UserServices();
        $this->settingRepository = new SettingRepository();
    }

    public function attendanceList(Request $request)
    {
        $_attendances = $this->attendanceServices->getList($request);
        $_date = $request->date ?? Helper::smTodayInYmd();
        $enableLunchInOut = $this->settingRepository->getSettingByKey('enable_lunch_in_out')->value;
        return view($this->basePath . "index", compact('_attendances', '_date', 'enableLunchInOut'));
    }

    public function attendanceCreate()
    {
        $_users = $this->userServices->getSelectList();
        $_date = Helper::smTodayInYmd();
        $enableLunchInOut = $this->settingRepository->getSettingByKey('enable_lunch_in_out')->value;
        if (checkUserRole()) {
            return view($this->basePath . "create", compact('_users', '_date', 'enableLunchInOut'));
        }
        if (checkUserPermission()) {
            return view($this->basePath . "create", compact('_users', '_date', 'enableLunchInOut'));
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");

    }

    public function attendanceSaveDetail(AttendanceCreateRequest $request)
    {
        try {
            $this->attendanceServices->saveAttendanceDetail($request);
            alert()->success('Success', 'Attendance been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.attendance.attendanceList");
    }

    public function attendanceEdit($attendance_id)
    {
        $_attendance = $this->attendanceServices->getAttendance($attendance_id);
        $_user = $this->userServices->getUser($_attendance->user_id);
        $_shift = $_user->getShift;
        $_date = $_attendance->date;
        $enableLunchInOut = $this->settingRepository->getSettingByKey('enable_lunch_in_out')->value;
        if (checkUserRole()) {
            return view($this->basePath . "edit", compact('_attendance', '_user', '_shift', '_date', 'enableLunchInOut'));
        }
        if (checkUserPermission()) {
            return view($this->basePath . "edit", compact('_attendance', '_user', '_shift', '_date', 'enableLunchInOut'));
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    public function updateAttendanceDetail(AttendanceRequest $request)
    {
        try {
            $this->attendanceServices->updateAttendanceDetail($request);
            alert()->success('Success', 'Attendance been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.attendance.attendanceList");
    }

    public function deleteAttendance($attendance_id)
    {
        try {
            $this->attendanceServices->deleteAttendance($attendance_id);
            alert()->success('Success', 'Attendance been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.attendance.attendanceList");
    }


    public function checkInEmployee($user_id ,Request $request)
    {
        try {
            DB::beginTransaction();
            $attendance_date = $request->attendance_date;
            $this->attendanceServices->attendanceUpdate(user_id: $user_id, attendanceType: 'checkIn' , attendance_date: $attendance_date);
            DB::commit();
            alert()->success('Success', 'Check In successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->back();
    }

    public function checkOutEmployee($user_id, $attendance_id ,Request $request)
    {
        try {
            DB::beginTransaction();
            $attendance_date = $request->attendance_date;
            $this->attendanceServices->attendanceUpdate(user_id:$user_id, attendanceType:'checkOut', attendance_id:$attendance_id , attendance_date: $attendance_date);
            DB::commit();
            alert()->success('Success', 'Check Out successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->back();
    }

    public function lunchCheckInEmployee($user_id, $attendance_id ,Request $request)
    {
        try {
            DB::beginTransaction();
            $attendance_date = $request->attendance_date;
            $this->attendanceServices->attendanceUpdate(user_id:$user_id, attendanceType:'lunchIn', attendance_id:$attendance_id , attendance_date: $attendance_date);
            DB::commit();
            alert()->success('Success', 'Lunch Check In successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->back();
    }

    public function lunchCheckOutEmployee($user_id, $attendance_id ,Request $request)
    {
        try {
            DB::beginTransaction();
            $attendance_date = $request->attendance_date;
            $this->attendanceServices->attendanceUpdate(user_id: $user_id, attendanceType: 'lunchOut', attendance_id: $attendance_id , attendance_date: $attendance_date);
            DB::commit();
            alert()->success('Success', 'Lunch Check Out successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->back();
    }

    public function monthlyAttendanceDetail($user_id, Request $request)
    {
        if (checkUserRole()) {
            return $this->getMonthlyAttendance($request, $user_id);
        }
        if (checkUserPermission()) {
            return $this->getMonthlyAttendance($request, $user_id);
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|Application|RedirectResponse
     */
    private function getMonthlyAttendance(Request $request, $user_id): \Illuminate\Contracts\Foundation\Application|RedirectResponse|Factory|Application|View
    {
        try {
            $year = $request->year ?? date('Y');
            $month = $request->month ?? date('m');
            $monthList = Helper::getMonthList();
            $userDetail = $this->userServices->getUser($user_id);
            $enableLunchInOut = $this->settingRepository->getSettingByKey('enable_lunch_in_out')->value;
            DB::beginTransaction();
            [
                $attendanceDetail,
                $absentCount,
                $presentCount,
                $totalWorkedHours,
                $leaveCount,
                $totalWorkingHours,
                $total_days_of_month
            ] = $this->attendanceServices->monthlyAttendanceDetail($user_id, $year, $month);
            DB::commit();
            return view($this->basePath . "monthly", compact('enableLunchInOut', 'attendanceDetail', 'absentCount', 'presentCount', 'totalWorkedHours', 'leaveCount', 'totalWorkingHours', 'monthList', 'userDetail', 'total_days_of_month'));
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "attendanceList");
    }

    public function downloadExcelAttendanceDetail($user_id, Request $request)
    {
        try {
            $year = $request->year ?? date('Y');
            $month = $request->month ?? date('m');
            $userDetail = $this->userServices->getUser($user_id);
            return Excel::download(new AttendanceExport($user_id, $year, $month), "Attendance-Detail-of-" . $userDetail->name . "-" . $year . "-" . $month . ".xlsx");
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "attendanceList");
    }


}
