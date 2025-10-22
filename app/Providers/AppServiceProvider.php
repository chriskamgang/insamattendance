<?php

namespace App\Providers;

use App\Helper\Helper;
use App\Http\Repositories\SettingRepository;
use App\Http\Services\CompanyDetailServices;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('company_details')) {
                $companyDetailServices = new CompanyDetailServices();
                $_companyDetail = $companyDetailServices->getCompanyDetail();

                view()->composer('auth.login', function ($view) use ($_companyDetail) {
                    $view->with('_companyDetail', $_companyDetail);
                });
                view()->composer('auth.forgetPassword', function ($view) use ($_companyDetail) {
                    $view->with('_companyDetail', $_companyDetail);
                });
                view()->composer('auth.otpView', function ($view) use ($_companyDetail) {
                    $view->with('_companyDetail', $_companyDetail);
                });
                view()->composer('auth.passwordResetView', function ($view) use ($_companyDetail) {
                    $view->with('_companyDetail', $_companyDetail);
                });

                view()->composer('include.header', function ($view) use ($_companyDetail) {
                    $view->with('_companyDetail', $_companyDetail);
                });
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs de connexion DB au démarrage
        }
    }
}
