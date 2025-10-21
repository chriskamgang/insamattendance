<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Http\Services\LeaveServices;
use App\Http\Services\LeaveTypeServices;
use App\Http\Services\UserServices;
use App\Models\Attendance;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LeaveController extends Controller
{
    private string $basePath = "leave.";
    private string $routePath = "admin.leave.";
    private string $error_message = "Oops! Something went wrong.";
    private LeaveServices $leaveServices;
    private LeaveTypeServices $leaveTypeServices;
    private UserServices $userServices;


    public function __construct()
    {
        $this->leaveServices = new LeaveServices();
        $this->leaveTypeServices = new LeaveTypeServices();
        $this->userServices = new UserServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $_leaves = $this->leaveServices->leaveList();
        return view($this->basePath . "index", compact('_leaves'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        $_leaveTypes = $this->leaveTypeServices->getSelectList();
        $_users = $this->userServices->getSelectList();
        return view($this->basePath . "create", compact('_leaveTypes', '_users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LeaveRequest $request
     * @return RedirectResponse
     */
    public function store(LeaveRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->leaveServices->saveLeave($request);
            alert()->success('Success', 'Leave been created successfully');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }
    public function edit(int $leave_group_code)
    {
        try {
            $_leaveTypes = $this->leaveTypeServices->getSelectList();
            $_users = $this->userServices->getSelectList();
            $_leave = $this->leaveServices->leaveEdit($leave_group_code);
            return view($this->basePath . "edit", compact('_leaveTypes', '_users','_leave'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function update(LeaveRequest $request , int $leave_group_code): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->leaveServices->updateLeave($request , $leave_group_code);
            alert()->success('Success', 'Leave been Updated successfully');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function delete(int $leave_group_code): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $this->leaveServices->deleteLeave($leave_group_code);
            alert()->success('Success', 'Leave been Updated successfully');
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

}
