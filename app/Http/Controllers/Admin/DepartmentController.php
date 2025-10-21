<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Http\Services\DepartmentServices;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class DepartmentController extends Controller

{
    private string $basePath = "department.";
    private string $routePath = "admin.department.";
    private string $error_message = "Oops! Something went wrong.";
    private DepartmentServices $departmentServices;


    public function __construct()
    {
        $this->departmentServices = new DepartmentServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $_departments = $this->departmentServices->getList();
        return view($this->basePath . "index", compact('_departments'));
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
     * @param DepartmentRequest $request
     * @return RedirectResponse
     */
    public function store(DepartmentRequest $request): RedirectResponse
    {
        if (checkUserRole()) {
            try {
                $this->departmentServices->saveDepartment($request);
                alert()->success('Success', 'Department been created successfully');
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $this->departmentServices->saveDepartment($request);
                alert()->success('Success', 'Department been created successfully');
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
                $_department = $this->departmentServices->getDepartment($id);
                return view($this->basePath . "edit", compact('_department'));
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $_department = $this->departmentServices->getDepartment($id);
                return view($this->basePath . "edit", compact('_department'));
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
     * @param DepartmentRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(DepartmentRequest $request, int $id): RedirectResponse
    {
        try {
            $this->departmentServices->updateDepartment($id, $request);
            alert()->success('Success', 'Department been updated successfully');
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
            $this->departmentServices->deleteDepartment($id);
            alert()->success('Success', 'Department has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function changeStatus(int $id): array
    {
        try {
            return $this->departmentServices->changeStatus($id);
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
