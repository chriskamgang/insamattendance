<?php

namespace App\Http\Middleware;

use App\Http\Repositories\SettingRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()) {
            $settingRepository = new SettingRepository();
            $timezone = $settingRepository->getSettingByKey('timezone');
            $timezone_value = (($timezone->value) ?: 'Asia/Kathmandu');
            config(['app.timezone' => $timezone_value]);
            date_default_timezone_set($timezone_value);
        }
        if (auth('api')->user()) {
            $settingRepository = new SettingRepository();
            $timezone = $settingRepository->getSettingByKey('timezone');
            $timezone_value = (($timezone->value) ?: 'Asia/Kathmandu');
            config(['app.timezone' => $timezone_value]);
            date_default_timezone_set($timezone_value);
        }
        return $next($request);
    }
}
