<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HolidayRequest;
use App\Http\Services\HolidayServices;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class HolidayController extends Controller
{
    private string $basePath = "holiday.";
    private string $routePath = "admin.holiday.";
    private string $error_message = "Oops! Something went wrong.";
    private HolidayServices $holidayServices;


    public function __construct()
    {
        $this->holidayServices = new HolidayServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $_holidays = $this->holidayServices->getList();
        return view($this->basePath . "index", compact('_holidays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        if (checkUserRole()) {
            return view($this->basePath . "create");
        }
        if (checkUserPermission()) {
            return view($this->basePath . "create");
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param HolidayRequest $request
     * @return RedirectResponse
     */
    public function store(HolidayRequest $request): RedirectResponse
    {
        try {
            $this->holidayServices->saveHoliday($request);
            alert()->success('Success', 'Holiday been created successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function edit(int $id)
    {
        try {
            $_holiday = $this->holidayServices->getHoliday($id);
            return view($this->basePath . "edit", compact('_holiday'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param HolidayRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(HolidayRequest $request, int $id): RedirectResponse
    {
        try {
            $this->holidayServices->updateHoliday($id, $request);
            alert()->success('Success', 'Holiday been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->holidayServices->deleteHoliday($id);
            alert()->success('Success', 'Holiday has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function changeStatus(int $id): array
    {
        try {
            return $this->holidayServices->changeStatus($id);
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
