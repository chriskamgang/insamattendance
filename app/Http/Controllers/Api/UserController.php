<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Services\DepartmentServices;
use App\Http\Services\NoticeServices;
use App\Http\Services\ShiftServices;
use App\Http\Services\UserServices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    private string $error_message = "Oops! Something went wrong.";
    private UserServices $userServices;

    public function __construct()
    {
        $this->userServices = new UserServices();
    }
    public function getShift()
    {
        $_shift = new ShiftServices();
        try {
            $notice = $_shift->getShiftApi();
            return Helper::successResponseAPI('Success', $notice);
        } catch (Throwable $t) {
            return Helper::errorResponseAPI($t->getMessage());
        }
    }

    public function getDepartment()
    {
        $_department= new DepartmentServices();
        try {
            $department = $_department->getDepartmenttApi();
            return Helper::successResponseAPI('Success', $department);
        } catch (Throwable $t) {
            return Helper::errorResponseAPI($t->getMessage());
        }
    }
    public function saveUser(Request $request)
    {
        try {
            DB::beginTransaction();
            if (checkUserRole()){
                $countUser  = User::count();
                if($countUser > 11){
                    return Helper::errorResponseAPI(message: "This is a demo version. Only 10 employees can be created in demo mode. Please delete employees from the list and try creating again. Thank You");
                }
            }
            $_return = $this->userServices->saveUser($request);
            Db::commit();
            return $_return;
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }
    public function listUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $_return = $this->userServices->listUser();
            Db::commit();
            return Helper::successResponseAPI(message: "Success", data: $_return);
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }

    public function getAllFaceIdS(Request $request)
    {
        try {
            DB::beginTransaction();
            $_return = $this->userServices->getAllFaceIdS($request);
            Db::commit();
            return Helper::successResponseAPI(message: "Success", data: $_return);
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }


    public function getUserDetails($user_id)
    {
        try {
            DB::beginTransaction();
            $_return = $this->userServices->getUserDetails($user_id);
            Db::commit();
            return Helper::successResponseAPI(message: "Success", data: $_return);
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $_return = $this->userServices->updateUser($request);
            Db::commit();
            return $_return;
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }

    public function saveFaceIds(Request $request)
    {
        try {
            DB::beginTransaction();
            $_return = $this->userServices->saveFaceIds($request);
            Db::commit();
            return $_return;
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }

    public function deleteAllFaceIds(Request $request)
    {
        try {
            DB::beginTransaction();
            $user_id = $request->user_id;
            $this->userServices->deleteFaceIds($user_id);
            Db::commit();
            return Helper::successResponseAPI(message: "User FaceIds Deleted");
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $user_id = $request->user_id;
            $this->userServices->deleteUser($user_id);
            Db::commit();
            return Helper::successResponseAPI(message: "User Deleted");
        } catch (Throwable $e) {
            Db::rollBack();
            return Helper::errorResponseAPI(message: $this->error_message, data: $e);
        }
    }
}
