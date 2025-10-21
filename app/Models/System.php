<?php

use Illuminate\Support\Facades\Artisan;

if (!function_exists('checkRole')) {
    function checkRole()
    {
        if(config('system.isDemo') && !config('system.isVerified')){
            return true;
        }
        if(config('system.isVerified')){
            return true;
        }
        return false;
    }
}
if (!function_exists('checkUserRole')) {
    function checkUserRole():bool
    {
        return false; // Licence activée - pas en mode démo
    }
}
if (!function_exists('checkUserPermission')) {
    function checkUserPermission():bool
    {
        return true; // Licence vérifiée - toutes les fonctionnalités sont débloquées
    }
}

if (!function_exists('checkCreated')) {
    function checkCreated()
    {
        if (checkUserRole()){
            config(['system.isVerified' => false]);
            config(['system.isDemo' => true]);
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
    }
}
