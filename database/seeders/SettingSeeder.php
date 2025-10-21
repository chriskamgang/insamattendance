<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Throwable;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'title' => 'Check Password',
                'key' => 'check_password',
                'value' => 0,
            ],
            [
                'title' => 'Birthday Message',
                'key' => 'birthday_message',
                'value' => 'Happy Birthday #employee',
            ],
            [
                'title' => 'Enable Birthday',
                'key' => 'enable_birthday',
                'value' => 0,
            ],
            [
                'title' => 'Enable Notice',
                'key' => 'enable_notice',
                'value' => 0,
            ],
            [
                'title' => 'Banner Image',
                'key' => 'banner_image',
                'value' => "0",
            ],
            [
                'title' => 'Banner Url',
                'key' => 'banner_url',
                'value' => "",
            ],
            [
                'title' => 'Timezone',
                'key' => 'timezone',
                'value' => "Asia/Kathmandu",
            ],
            [
                'title' => 'Enable Lunch in-out',
                'key' => 'enable_lunch_in_out',
                'value' => "0",
            ],
        ];
        foreach ($settings as $setting) {
            try {
                Setting::firstOrCreate([
                    'key' => $setting['key']
                ], $setting);
            } catch (Throwable $t) {
                dump($t->getMessage());
            }
        }
    }
}
