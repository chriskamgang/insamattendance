<?php

namespace App\Http\Services;

use App\Helper\Helper;
use App\Http\Repositories\SettingRepository;

class SettingServices
{
    private SettingRepository $settingRepository;

    public function __construct()
    {
        $this->settingRepository = new SettingRepository();
    }

    public function getAllData()
    {
        return $this->settingRepository->getAll();
    }

    public function appSettingSave($request)
    {
        if ($request->hasFile('banner_image')) {
            $banner_image= $this->settingRepository->getSettingByKey('banner_image');
            if($banner_image){
                Helper::unlinkUploadedFile($banner_image->value, "setting");
            }
            $_image = Helper::uploadFile(file: $request->banner_image, file_folder_name: "setting");
            $this->settingRepository->update($banner_image, $_image);
        }
        $banner_url = $this->settingRepository->getSettingByKey('banner_url');
        $this->settingRepository->update($banner_url, $request->banner_url);

        $check_password = $this->settingRepository->getSettingByKey('check_password');
        $this->settingRepository->update($check_password, $request->check_password);

        $enable_notice = $this->settingRepository->getSettingByKey('enable_notice');
        $this->settingRepository->update($enable_notice, $request->enable_notice);

        $enable_birthday = $this->settingRepository->getSettingByKey('enable_birthday');
        $this->settingRepository->update($enable_birthday, $request->enable_birthday);

        $birthday_message = $this->settingRepository->getSettingByKey('birthday_message');
        $this->settingRepository->update($birthday_message, $request->birthday_message);

        $timezone = $this->settingRepository->getSettingByKey('timezone');
        $this->settingRepository->update($timezone, $request->timezone);

        $enable_lunch_in_out = $this->settingRepository->getSettingByKey('enable_lunch_in_out');
        $this->settingRepository->update($enable_lunch_in_out, $request->enable_lunch_in_out);

    }

}
