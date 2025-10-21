<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Repositories\SettingRepository;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpVerificationRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Services\UserServices;
use App\Mail\OtpVerifyMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    private string $error_message = "Oops! Something went wrong.";
    private UserServices $userServices;
    private SettingRepository $settingRepository;

    public function __construct()
    {
        $this->userServices = new UserServices();
        $this->settingRepository = new SettingRepository();
    }


    public function login(): View|Factory|string|Application
    {
        if (Auth::check()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return view('auth.login');
    }

    public function passwordReset(): View|Factory|string|Application
    {
        if (Auth::check()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return view('auth.forgetPassword');
    }
    public function otpView(): View|Factory|string|Application
    {
        if (Auth::check()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return view('auth.otpView');
    }
    public function passwordResetView($unique_code): View|Factory|string|Application
    {
        if (Auth::check()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        $user = $this->userServices->checkUniqueCode($unique_code);
        if($user) {
            return view('auth.passwordResetView' , compact('unique_code'));
        }
        return redirect()->route('admin.login')->withInput()->withErrors(['login_error' => "Invalid request. Please Try again "]);
    }


    public function getAuthenticate(): Redirector|Application|RedirectResponse
    {
        return redirect(route('admin.login'));
    }


    public function sendPasswordResetLink(PasswordResetRequest $request)
    {
        try {
            $data = $request->all();
            DB::beginTransaction();
            $user = $this->userServices->checkUserByEmail($data['email']);
            if($user) {
                $this->userServices->sendPasswordResetLink($user);
                Mail::to($user->email)->send(new OtpVerifyMail($user));
                Db::commit();
                return redirect()->route('admin.otpView');
            }
            return redirect()->back()->withInput()->withErrors(['email' => "Email do not match our records."]);
        } catch (Throwable $e) {
            Db::rollBack();
            return redirect()->back()->withInput()->withErrors(['login_error' => $e->getMessage()]);
        }

    }
    public function otpVerification(OtpVerificationRequest $request)
    {
        try {
            $user = $this->userServices->checkTwoFactorCode($request);
            if($user) {
                DB::beginTransaction();
                $unique_code = $this->userServices->getAndUpdateUniqueCode($user);
                Db::commit();
               return redirect()->route('admin.passwordResetView',$unique_code);
            }
            return redirect()->back()->withInput()->withErrors(['two_factor_code' => "Invalid Otp Code"]);
        } catch (Throwable $e) {
            Db::rollBack();
            return redirect()->back()->withInput()->withErrors(['login_error' => $e->getMessage()]);
        }
    }

    public function updatePassword(PasswordRequest $request, $unique_code): RedirectResponse
    {
        try {

            $user = $this->userServices->checkUniqueCode($unique_code);
            if($user) {
                DB::beginTransaction();
                $this->userServices->resetPassword($user,$request);
                Db::commit();
                return redirect()->route('admin.login')->withInput()->withErrors(['login_success' => "Password Updated."]);
            }
            return redirect()->route('admin.login')->withInput()->withErrors(['login_error' => "Invalid request. Please Try again "]);

        } catch (Throwable $e) {
            Db::rollBack();
            return redirect()->back()->withInput()->withErrors(['login_error' => $e->getMessage()]);
        }
    }



    /**
     * @param LoginRequest $request
     * @return Redirector|Application|RedirectResponse
     */
    public function authenticate(LoginRequest $request): Redirector|Application|RedirectResponse
    {
        $return_message_array = [];
        try {
            $data = $request->all();
            DB::beginTransaction();
            $user = $this->userServices->checkUserByEmail($data['username']);
            if($user) {
                $check_password = Helper::checkPassword($data['password'], $user->password);
                if ($check_password) {
                    $credential = [
                        'email' => $user->email,
                        'password' => Helper::getSaltedPassword($data['password'])
                    ];
                    if ($this->hasTooManyLoginAttempts($request)) {
                        $this->fireLockoutEvent($request);
                        $this->sendLockoutResponse($request);
                    }
                    if (Auth::attempt($credential)) {
                        $this->clearLoginAttempts($request);
                        Helper::checkUrl();
                        return redirect()->intended(route('admin.dashboard'));
                    }
                    $this->incrementLoginAttempts($request);
                    $return_message_array = ['username' => "These credentials do not match our records."];
                } else {
                    $return_message_array = ['password' => "These credentials do not match our records."];
                }
            } else {
                return redirect()->back()->withInput()->withErrors(['username' => "Username do not match our records."]);
            }
            Db::commit();
        } catch (Throwable $e) {
            Db::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->back()->withInput()->withErrors($return_message_array);
    }

    /**
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     */
    public function logout(Request $request): Redirector|Application|RedirectResponse
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect(route('admin.login'));
    }
}
