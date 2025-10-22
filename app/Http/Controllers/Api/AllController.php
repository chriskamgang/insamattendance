<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\SettingRepository;
use App\Http\Services\CompanyDetailServices;
use App\Http\Services\HolidayServices;
use App\Http\Services\NoticeServices;
use App\Http\Services\UserServices;
use App\Models\Notice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AllController extends Controller
{
    public function getAllNotice()
    {
        $_notice = new NoticeServices();
        try {
            $notice = $_notice->getAllNoticeApi();
            $return_data['notice'] = $notice;
            $return_data['birthday'] = [];

            $date = now();
            $future_date = now()->addDays(7);

            $_users = User::select(DB::raw("DATE_FORMAT(dob,'%m-%d') as months"), 'name', 'dob')
                ->where(function ($query) use ($date , $future_date) {
                    $query->whereMonth('dob', '=', $date->month)
                        ->whereDay('dob', '>=', $date->day)
                        ->whereMonth('dob', '<=', $future_date->month)
                        ->whereDay('dob', '<=', $future_date->day);
                })->orderBy('dob', 'asc')->get();
            foreach ($_users as $user) {
                $return_data['birthday'][] = ['name'=>$user->name ,'date'=>$user->months];
            }
            return Helper::successResponseAPI('Success', $return_data);
        } catch (Throwable $t) {
            return Helper::errorResponseAPI($t->getMessage());
        }
    }

    public function getCurrentHolidayList()
    {
        $_holidayServices = new HolidayServices();
        try {
            $holiday = $_holidayServices->getCurrentHolidayListApi();
            return Helper::successResponseAPI('Success', $holiday);
        } catch (Throwable $t) {
            return Helper::errorResponseAPI($t->getMessage());
        }
    }

    public function getCompanyDetails()
    {
        $_companyDetailService = new CompanyDetailServices();
        try {
            $companyDetail = $_companyDetailService->getCompanyDetailApi();
            return Helper::successResponseAPI('Success', $companyDetail);
        } catch (Throwable $t) {
            return Helper::errorResponseAPI($t->getMessage());
        }
    }

    public function updateCompanyDetails(Request $request)
    {
        $_companyDetailService = new CompanyDetailServices();
        try {
            return $_companyDetailService->companyDetailUpdateApi($request);

        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $e->getMessage(), data: $e);
        }
    }

    public function dashboard()
    {
        try {
            $_settingRepository = new SettingRepository();
            $data['check_password'] = $_settingRepository->getSettingByKey('check_password')->value;

            $banner_image = $_settingRepository->getSettingByKey('banner_image');
            $data['banner_image'] = ($banner_image) ? asset('uploads/setting/' . $banner_image->value) : "";
            $data['banner_url'] = $_settingRepository->getSettingByKey('banner_url')->value ?? "";
            $return_notice = "";
            $return_birthday_message = "";
            $enable_notice = $_settingRepository->getSettingByKey('enable_notice')->value;
            $enable_birthday = $_settingRepository->getSettingByKey('enable_birthday')->value;
            if ($enable_notice) {
                $_notices = Notice::where('is_active', 1)
                    ->whereDate('start_date', '<=', Helper::smTodayInYmd())
                    ->whereDate('end_date', '>=', Helper::smTodayInYmd())
                    ->get();
                foreach ($_notices as $notice) {
                    $return_notice .= " *** " . $notice->description . " *** ";
                }
            }
            if ($enable_birthday) {
                $birthday_message = $_settingRepository->getSettingByKey('birthday_message')->value;
                $date = now();
                $_users = User::whereMonth('dob', '>', $date->month)
                    ->orWhere(function ($query) use ($date) {
                        $query->whereMonth('dob', $date->month)
                            ->whereDay('dob', $date->day);
                    })->get();
                foreach ($_users as $user) {
                    $return_birthday_message .= " !!! " . str_replace("#employee", $user->name, $birthday_message) . " !!! ";
                }
            }

            $data['notice_birthday'] = $return_birthday_message . " " . $return_notice;
            $_companyDetailService = new CompanyDetailServices();
            $data['company_detail'] = $_companyDetailService->getCompanyDetailApi();
            $_user = User::where('user_type','admin')->first();
            $data['app_password']  = $_user->app_password;
            return Helper::successResponseAPI('Success', $data);
        } catch (Throwable $t) {
            return Helper::errorResponseAPI($t->getMessage());
        }
    }


}
