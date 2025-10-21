<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Helper\TimezoneHelper;
use App\Http\Controllers\Controller;
use App\Http\Services\SettingServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SettingController extends Controller
{
    private string $error_message = "Oops! Something went wrong.";
    protected SettingServices $settingServices;

    public function __construct()
    {
        $this->settingServices = new SettingServices();
    }
    public function appSetting()
    {
        Helper::checkUrl();
        $_setting = $this->settingServices->getAllData();
        $timezoneArray = TimezoneHelper::getTimezone();
        return view("setting.appSetting" ,compact('_setting' , 'timezoneArray'));
    }
    public function appSettingSave(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->settingServices->appSettingSave($request);
            Helper::checkUrl();
            DB::commit();
            alert()->success('Success', 'Updated successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.setting.appSetting");
    }
}
