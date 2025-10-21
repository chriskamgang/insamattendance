<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserRequest;
use App\Http\Services\DepartmentServices;
use App\Http\Services\ShiftServices;
use App\Http\Services\UserServices;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller

{
    private string $basePath = "user.";
    private string $routePath = "admin.user.";
    private string $error_message = "Oops! Something went wrong.";
    private UserServices $userServices;
    private ShiftServices $shiftServices;
    private DepartmentServices $departmentServices;


    public function __construct()
    {
        $this->userServices = new UserServices();
        $this->shiftServices = new ShiftServices();
        $this->departmentServices = new DepartmentServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        $_users = $this->userServices->getList($request);
        $_shifts = $this->shiftServices->getSelectList();
        $_department = $this->departmentServices->getSelectList();
        return view($this->basePath . "index", compact('_users','_shifts','_department'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {

        $_shifts = $this->shiftServices->getSelectList();
        $_department = $this->departmentServices->getSelectList();
        if (checkUserRole()) {
            $countUser  = User::count();
            if($countUser > 11){
                alert()->error('This is a demo version. Only 10 employees can be created in demo mode. Please delete employees from the list and try creating again. Thank You');
                return redirect()->route($this->routePath . "index");
            }
            return view($this->basePath . "create", compact('_shifts','_department'));
        }
        if (checkUserPermission()) {
            return view($this->basePath . "create", compact('_shifts','_department'));
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");

    }
    public function listAdmin()
    {
        $_users = $this->userServices->getAdminList();
        return view($this->basePath . "indexAdmin", compact('_users'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param UserCreateRequest $request
     * @return RedirectResponse
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        try {
            $this->userServices->saveUserWeb($request);
            alert()->success('Success', 'User been created successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }
    public function createAdmin()
    {
        if (checkUserRole()) {
            $countUser  = User::count();
            if($countUser > 11){
                alert()->error('This is a demo version. Only 10 employees can be created in demo mode. Please delete employees from the list and try creating again. Thank You');
                return redirect()->route($this->routePath . "index");
            }
            return view($this->basePath . "createAdmin");
        }
        if (checkUserPermission()) {
            return view($this->basePath . "createAdmin");
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }
    public function saveAdmin(UserRequest $request): RedirectResponse
    {
        try {
            $this->userServices->saveAdminWeb($request);
            alert()->success('Success', 'User been created successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
    }

    public function editAdmin(int $id)
    {
        try {
            $_user = $this->userServices->getUser($id);
            return view($this->basePath . "editAdmin", compact('_user'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
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
            $_user = $this->userServices->getUser($id);
            $_shifts = $this->shiftServices->getSelectList();
            $_department = $this->departmentServices->getSelectList();
            return view($this->basePath . "edit", compact('_user','_shifts' ,'_department'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UserRequest $request, int $id): RedirectResponse
    {
        try {
            $this->userServices->updateUserWeb($request , $id);//need to update
            alert()->success('Success', 'User been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function updateAdmin(UserRequest $request, int $id): RedirectResponse
    {
        try {
            $this->userServices->updateUserWeb($request , $id);
            alert()->success('Success', 'User been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
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
            if (checkUserRole()) {
                $user = $this->userServices->getUser($id);
                if($user->email == "admin@admin.com"){
                    alert()->error('This is a demo version. Cannot delete Admin. Thank You');
                    return redirect()->route("admin.admin.listAdmin");
                }
            }
            $this->userServices->deleteUser($id);
            alert()->success('Success', 'User has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->back();
    }
    public function deleteFaceIds(int $id): RedirectResponse
    {
        try {
            $this->userServices->deleteFaceIds($id);
            alert()->success('Success', 'User FaceIds has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function changePassword(int $id)
    {
        try {
            $_user = $this->userServices->getUser($id);
            return view($this->basePath . "editPassword", compact('_user'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
    }

    public function savePassword(Request $request, int $id): RedirectResponse
    {
        try {
            if (checkUserRole()) {
                alert()->error('This is a demo version. Cannot Update Password. Thank You');
                return redirect()->route("admin.admin.listAdmin");
            }
            DB::beginTransaction();
            $this->userServices->updatePassword($id, $request);
            alert()->success('Success', 'Password has been updated successfully');
            Db::commit();
        } catch (Throwable $e) {
            Db::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
    }

    public function changeAppPassword(int $id)
    {
        try {
            $_user = $this->userServices->getUser($id);
            return view($this->basePath . "editAppPassword", compact('_user'));
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
    }

    public function saveAppPassword(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if (checkUserRole()) {
                alert()->error('This is a demo version. Cannot Update Password. Thank You');
                return redirect()->route("admin.admin.listAdmin");
            }
            $this->userServices->updateAppPassword($id, $request);
            alert()->success('Success', 'App Password has been updated successfully');
            Db::commit();
        } catch (Throwable $e) {
            Db::rollBack();
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route("admin.admin.listAdmin");
    }


}
