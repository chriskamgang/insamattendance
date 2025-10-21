<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShiftRequest;
use App\Http\Services\ShiftServices;
use App\Models\Shift;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class ShiftController extends Controller
{
    private string $basePath = "shift.";
    private string $routePath = "admin.shift.";
    private string $error_message = "Oops! Something went wrong.";
    private ShiftServices $shiftServices;


    public function __construct()
    {
        $this->shiftServices = new ShiftServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $_shifts = $this->shiftServices->getList();
        return view($this->basePath . "index", compact('_shifts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        $shiftType = Shift::SHIFT;
        if (checkUserRole()) {
            return view($this->basePath . "create",compact('shiftType'));
        }
        if (checkUserPermission()) {
            return view($this->basePath . "create",compact('shiftType'));
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ShiftRequest $request
     * @return RedirectResponse
     */
    public function store(ShiftRequest $request): RedirectResponse
    {
        if (checkUserRole()) {
            try {
                $this->shiftServices->saveShift($request);
                alert()->success('Success', 'Shift been created successfully');
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $this->shiftServices->saveShift($request);
                alert()->success('Success', 'Shift been created successfully');
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
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
        $shiftType = Shift::SHIFT;
        if (checkUserRole()) {
            try {
                $_shift = $this->shiftServices->getShift($id);
                return view($this->basePath . "edit", compact('_shift','shiftType'));
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $_shift = $this->shiftServices->getShift($id);
                return view($this->basePath . "edit", compact('_shift','shiftType'));
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ShiftRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(ShiftRequest $request, int $id): RedirectResponse
    {
        try {
            $this->shiftServices->updateShift($id, $request);
            alert()->success('Success', 'Shift been updated successfully');
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
            $this->shiftServices->deleteShift($id);
            alert()->success('Success', 'Shift has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function changeStatus(int $id): array
    {
        try {
            return $this->shiftServices->changeStatus($id);
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
