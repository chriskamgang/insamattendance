<?php

namespace App\Http\Repositories;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingRepository
{
    private Setting $setting;

    public function __construct()
    {
        $this->setting = new Setting();
    }

    public function getAll()
    {
        return  $this->setting->get()->pluck('value' ,'key');
    }

    public function getSettingByKey($key)
    {
        return  $this->setting->where('key',$key)->first();
    }
    public function update($setting, $data): mixed
    {
        return DB::transaction(static function () use ($setting, $data) {
            return $setting->update(['value' => $data]);
        });
    }

}
