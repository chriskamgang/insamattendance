<?php

namespace Database\Seeders;

use App\Helper\Helper;
use App\Models\CompanyDetails;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            User::create([
                'name' => 'Administration',
                'dob' => '1889-04-20',
                'email' => 'admin@admin.com',
                'mobile' => '+1 (664) 609-7030',
                'address' => 'Austin, Texas, United States',
                'image' => '',
                'password' => Helper::passwordHashing("admin@admin.com"),
                'user_type' => 'admin',
            ]);

            CompanyDetails::create([
                'name' => "Company Name",
                'primary_email' => "primary_email@gmail.com",
                'primary_contact_no' => "1234567890",
                'address' => "Company Address",
                'updated_by' => 1,
            ]);
            $this->call([
                SettingSeeder::class,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            dump($e);
        }
    }


}
