<?php

namespace App\Exports;

use App\Exceptions\SMException;
use App\Http\Services\AttendanceServices;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
class AttendanceExport implements FromCollection
{

    private mixed $user_id;
    private mixed $year;
    private mixed $month;
    private AttendanceServices $attendanceServices;

    public function __construct($user_id, $year, $month)
    {
        $this->user_id = $user_id;
        $this->year = $year;
        $this->month = $month;
        $this->attendanceServices = new AttendanceServices();
    }

    /**
     * @throws SMException
     */
    public function collection()
    {
        $attendanceCollection = [[
            "#",
            "Name",
            "Date",
            "Check In",
            "Check Out",
            "Lunch In",
            "Lunch Out",
            "Is on leave",
            "Attendance Type",
            "Total Working Duration",
            "Total Lunch Duration",
            "Total Over-Time Duration",
            "Status",
        ]];

        $attendanceCollection []=$this->attendanceServices->attendanceDetailByDate($this->user_id ,  $this->year, $this->month);

        return new Collection($attendanceCollection);
    }
}
