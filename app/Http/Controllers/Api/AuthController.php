<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    private string $error_message = "Oops! Something went wrong.";
    private UserServices $userServices;

    public function __construct()
    {
        $this->userServices = new UserServices();
    }

    public function login(Request $request)
    {
        try {
            DB::beginTransaction();
            $_return = $this->userServices->userLogin($request);
            Db::commit();
            return $_return;
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }
    public function logout()
    {
        try {
            if (Auth::user()) {
                $user = Auth::user()->token();
                $user->revoke();
                return Helper::successResponseAPI(message: "Logout successfully");
            }
            return Helper::errorResponseAPI(message: 'Unable to Logout');
        } catch (Throwable $e) {
            return Helper::errorResponseAPI(message: 'Unable to Logout', data: $e);
        }
    }
}
