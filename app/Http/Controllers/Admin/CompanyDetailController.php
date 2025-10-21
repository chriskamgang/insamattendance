<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyDetailRequest;
use App\Http\Services\CompanyDetailServices;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class CompanyDetailController extends Controller
{
    private string $error_message = "Oops! Something went wrong.";
    private CompanyDetailServices $companyDetailServices;

    public function __construct()
    {
        $this->companyDetailServices = new CompanyDetailServices();
    }

    /**
     * Show the form for editing the specified resource.
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function companyDetail()
    {
        try {
            $_companyDetail = $this->companyDetailServices->getCompanyDetail();
            return view("companyDetail.companyDetail", compact('_companyDetail'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.dashboard");
    }

    /**
     * Update the specified resource in storage.
     * @param CompanyDetailRequest $request
     * @return RedirectResponse
     */
    public function companyDetailUpdate(CompanyDetailRequest $request): RedirectResponse
    {
        try {
            $this->companyDetailServices->companyDetailUpdate($request);
            alert()->success('Success', 'Company Detail been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.companyDetail.companyDetail");
    }
}
