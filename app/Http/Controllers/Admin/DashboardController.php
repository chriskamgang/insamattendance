<?php

namespace App\Http\Controllers\Admin;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\SettingRepository;
use App\Http\Services\AttendanceServices;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Notice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $total_employee = User::where('user_type' , "employee")->count();
        $total_holidays = Holiday::count();
        $total_on_leave = Attendance::where('date', Helper::smTodayInYmd())->where('is_on_leave')->count();
        $total_checked_in = Attendance::where('date', Helper::smTodayInYmd())->whereNotNull('check_in')->count();
        $total_lunch_in = Attendance::where('date', Helper::smTodayInYmd())->whereNotNull('lunch_in')->count();
        $total_lunch_out = Attendance::where('date', Helper::smTodayInYmd())->whereNotNull('lunch_out')->count();
        $total_checked_out = Attendance::where('date', Helper::smTodayInYmd())->whereNotNull('check_out')->count();


        $_settingRepository = new SettingRepository();
        $enable_notice = $_settingRepository->getSettingByKey('enable_notice')->value;
        $enable_birthday = $_settingRepository->getSettingByKey('enable_birthday')->value;
        $noticeMessage = "";
        if ($enable_notice) {
            $_notices = Notice::where('is_active', 1)
                ->whereDate('start_date', '<=', Helper::smTodayInYmd())
                ->whereDate('end_date', '>=', Helper::smTodayInYmd())
                ->get();
            foreach ($_notices as $notice) {
                $noticeMessage .= $notice->description . " --- ";
            }
        }
        $birthdayMessage = "";
        if($enable_birthday){
            $birthday_message = $_settingRepository->getSettingByKey('birthday_message')->value;
            $_users = User::whereMonth('dob', Carbon::now()->format('m'))
                ->whereDay('dob', Carbon::now()->format('d'))
                ->get();
            foreach ($_users as $user) {
                $birthdayMessage .= " !!! " . str_replace("#employee", $user->name, $birthday_message) . " !!! ";
            }
        }
        $attendanceServices = new AttendanceServices();
        $_attendances = $attendanceServices->getList($request);
        $_date = Helper::smTodayInYmd();
        $enableLunchInOut = $_settingRepository->getSettingByKey('enable_lunch_in_out')->value;
        return view('dashboard.dashboard' , compact('enableLunchInOut','noticeMessage','birthdayMessage','_date','total_employee' , 'total_holidays','total_on_leave','total_checked_in','total_lunch_in','total_lunch_out','total_checked_out','_attendances'));
    }
}
