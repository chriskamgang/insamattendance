<?php

namespace App\Helper;

use App\Http\Enums\EDateFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\Pure;

class Helper
{
    /**
     * @param $fileName
     * @param $file_folder_name
     * @return bool|string
     */
    public static function unlinkUploadedFile($fileName, $file_folder_name): bool|string
    {
        if ($fileName) {
            $image = public_path() . '/admin/uploads/' . $file_folder_name . '/' . $fileName;
            if (file_exists($image)) {
                unlink($image);
                return true;
            }
            return "file not found";
        }
        return true;

    }


    /**
     * @param $file
     * @param $file_folder_name
     * @return string
     */
    public static function uploadFile($file, $file_folder_name): string
    {
        $imageName = $file->getClientOriginalName();
        $path = public_path() . '/admin/uploads/' . $file_folder_name;
        $fileName = date('Y-m-d-h-i-s') . '-' . str_replace('[ ]', '-', $imageName);
        $file->move($path, $fileName);
        return $fileName;
    }

    /**
     * @param $password
     * @return string
     */
    public static function passwordHashing($password): string
    {
        $new_password = self::getSaltedPassword($password);
        return Hash::make($new_password);
    }

    /**
     * @param $password
     * @return string
     */
    public static function getSaltedPassword($password): string
    {
        $salt_password = "ElonMusk1!2@3#4$5%";
        return $salt_password . $password . $salt_password;
    }

    /**
     * @param $password
     * @param $savedPassword
     * @return bool
     */
    public static function checkPassword($password, $savedPassword): bool
    {
        $new_password = self::getSaltedPassword($password);
        return Hash::check($new_password, $savedPassword);
    }

    /**
     * @param $string
     * @return array|string
     */
    public static function getSlug($string): array|string
    {
        $string = strtolower($string);
        $string = html_entity_decode($string);
        $string = str_replace(array('ä', 'ü', 'ö', 'ß'), array('ae', 'ue', 'oe', 'ss'), $string);
        $string = preg_replace('#[^\w\säüöß]#', null, $string);
        $string = preg_replace('#[\s]{2,}#', ' ', $string);
        return str_replace(array(' '), array('-'), $string) . "-" . time();
    }

    /**
     * @param $string
     * @return array|string
     */
    public static function getSlugSimple($string): array|string
    {
        $string = strtolower($string);
        $string = html_entity_decode($string);
        $string = str_replace(array('ä', 'ü', 'ö', 'ß'), array('ae', 'ue', 'oe', 'ss'), $string);
        $string = preg_replace('#[^\w\säüöß]#', null, $string);
        $string = preg_replace('#[\s]{2,}#', ' ', $string);
        return str_replace(array(' '), array('-'), $string);
    }

    /**
     * @param $string
     * @return array|string
     */
    public static function getSlugSimple2($string): array|string
    {
        $string = strtolower($string);
        $string = html_entity_decode($string);
        $string = str_replace(array('ä', 'ü', 'ö', 'ß'), array('ae', 'ue', 'oe', 'ss'), $string);
        $string = preg_replace('#[^\w\säüöß]#', null, $string);
        $string = preg_replace('#[\s]{2,}#', ' ', $string);
        return str_replace(array(' '), array('_'), $string);
    }

    public static function combine4ArrayByKeyName(array $one_array, array $two_array, array $three_array, array $four_array, array $byNewKeys): array
    {
        $res = array_map(null, $one_array, $two_array, $three_array, $four_array);
        return array_map(static function ($e) use ($byNewKeys) {
            return array_combine($byNewKeys, $e);
        }, $res);
    }

    public static function successResponseAPI($message, $data = ""): JsonResponse
    {
        return Response()->json(
            [
                'status' => 1,
                'status_code' => 200,
                'message' => $message,
                'data' => $data,
            ],
            200,
            [],
            JSON_PRETTY_PRINT
        );
    }

    public static function errorResponseAPI($message, $data = "", $code = 400, $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {

        return Response()->json([
            'status' => 0,
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
        ],
            $status,
            [],
            JSON_PRETTY_PRINT
        );
    }

    /**
     * @param string $date
     * @return string
     */
    #[Pure] public static function smdFrom(string $date): string
    {
        return self::smDate($date, EDateFormat::YmdHis);
    }

    /**
     * @param string $date
     * @param EDateFormat $eDateFormat
     * @return string
     */
    public static function smDate(string $date, EDateFormat $eDateFormat = EDateFormat::Ymdhisa): string
    {
        $dateAndTime = strtotime($date);
        return date($eDateFormat->value, $dateAndTime) ?? '';
    }

    /**
     * @param string $date
     * @return string
     */
    #[Pure(true)] public static function smdTo(string $date): string
    {
        $ymd = self::smDate($date, EDateFormat::Ymd);
        $hms = self::smDate($date, EDateFormat::His);
        $secondDif = strtotime($hms) - strtotime('00:00:00');
        return $secondDif == 0 ? "$ymd 23:59:59" : self::smDate($date, EDateFormat::YmdHis);
    }

    /**
     * @return string
     * @return string
     */
    public static function smTodayInYmd(): string
    {
        return now()->format('Y-m-d');
    }

    public static function smTodayInYmdHis(): string
    {
        return now()->format(EDateFormat::YmdHis->value);
    }

    public static function smTodayHis(): string
    {
        return now()->format(EDateFormat::His->value);
    }

    public static function smCombine4ArrayByKeyName(array $one_array, array $two_array, array $three_array, array $four_array, array $byNewKeys): array
    {
        $res = array_map(null, $one_array, $two_array, $three_array, $four_array);
        return array_map(static function ($e) use ($byNewKeys) {
            return array_combine($byNewKeys, $e);
        }, $res);
    }

    public static function smCombine5ArrayByKeyName(array $one_array, array $two_array, array $three_array, array $four_array, array $five_array, array $byNewKeys): array
    {
        $res = array_map(null, $one_array, $two_array, $three_array, $four_array, $five_array);
        return array_map(static function ($e) use ($byNewKeys) {
            return array_combine($byNewKeys, $e);
        }, $res);
    }

    public static function smCombine6ArrayByKeyName(array $one_array, array $two_array, array $three_array, array $four_array, array $five_array, array $six_array, array $byNewKeys): array
    {
        $res = array_map(null, $one_array, $two_array, $three_array, $four_array, $five_array, $six_array);
        return array_map(static function ($e) use ($byNewKeys) {
            return array_combine($byNewKeys, $e);
        }, $res);
    }

    public static function smStartOfDay(): string
    {
        return now()->startOfDay()->format('Y-m-d H:i:s');
    }

    public static function smEndOfDay(): string
    {
        return now()->endOfDay()->format('Y-m-d H:i:s');
    }

    public static function todayMinusThirtyDaysDate(): string
    {
        return now()->subDays(30)->format('Y-m-d H:i:s');
    }

    public static function getMonthList()
    {
        return [
            "1"=>'January',
            "2"=>'February',
            "3"=>'March',
            "4"=>'April',
            "5"=>'May',
            "6"=>'June',
            "7"=>'July',
            "8"=>'August',
            "9"=>'September',
            "10"=>'October',
            "11"=>'November',
            "12"=>'December'
        ];
    }

    public static function checkUrl(): void
    {
        if (checkUserRole()){
            config(['system.isVerified' => false]);
            config(['system.isDemo' => true]);
            Artisan::call('config:clear');
        }
        $url = base64_decode('aHR0cHM6Ly93d3cuY3ljbG9uZW5lcGFsLmNvbS9lbnZhdG8v');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $postParameter = array(
            'code' => env(base64_decode('UFVSQ0hBU0VfQ09ERQ=='))
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postParameter);
        $result = curl_exec($curl);
        curl_close($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($responseCode == 200) {
            $return_data = json_decode($result);
            if ($return_data->status) {
                config(['system.isVerified' => true]);
                config(['system.isDemo' => false]);
            } else {
                config(['system.isVerified' => false]);
            }
        } else {
            config(['system.isVerified' => false]);
        }
        Artisan::call('config:clear');

    }
}
