<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveTypeRequest;
use App\Http\Services\LeaveTypeServices;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class LeaveTypesController extends Controller
{
    private string $basePath = "leaveType.";
    private string $routePath = "admin.leaveType.";
    private string $error_message = "Oops! Something went wrong.";
    private LeaveTypeServices $leaveTypeServices;


    public function __construct()
    {
        $this->leaveTypeServices = new LeaveTypeServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $_leaveTypes = $this->leaveTypeServices->getList();
        return view($this->basePath . "index", compact('_leaveTypes'));
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
     * @param LeaveTypeRequest $request
     * @return RedirectResponse
     */
    public function store(LeaveTypeRequest $request): RedirectResponse
    {



        if (checkUserRole()) {
            try {
                $this->leaveTypeServices->saveLeaveType($request);
                alert()->success('Success', 'LeaveType been created successfully');
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $this->leaveTypeServices->saveLeaveType($request);
                alert()->success('Success', 'LeaveType been created successfully');
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
        if (checkUserRole()) {
            try {
                $_leaveType = $this->leaveTypeServices->getLeaveType($id);
                return view($this->basePath . "edit", compact('_leaveType'));
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $_leaveType = $this->leaveTypeServices->getLeaveType($id);
                return view($this->basePath . "edit", compact('_leaveType'));
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
     * @param LeaveTypeRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(LeaveTypeRequest $request, int $id): RedirectResponse
    {
        try {
            $this->leaveTypeServices->updateLeaveType($id, $request);
            alert()->success('Success', 'LeaveType been updated successfully');
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
            $this->leaveTypeServices->deleteLeaveType($id);
            alert()->success('Success', 'LeaveType has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function changeStatus(int $id): array
    {
        try {
            return $this->leaveTypeServices->changeStatus($id);
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
