<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Services\AttendanceServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AttendanceController extends Controller
{
    private AttendanceServices $attendanceServices;

    public function __construct()
    {
        $this->attendanceServices = new AttendanceServices();
    }

    public function employeeAttendance(Request $request)
    {
        try {
            DB::beginTransaction();
            $returnData = $this->attendanceServices->employeeAttendance($request);
            DB::commit();
            return $returnData;
        } catch (Throwable $t) {
            DB::rollBack();
            return Helper::errorResponseAPI($t->getMessage());
        }
    }

    public function getTodayAttendance(): ?JsonResponse
    {
        try {
            DB::beginTransaction();
            $returnData = $this->attendanceServices->getTodayAttendanceApi();
            DB::commit();
            return Helper::successResponseAPI('Success', $returnData);
        } catch (Throwable $t) {
            DB::rollBack();
            return Helper::errorResponseAPI($t->getMessage());
        }
    }
}
