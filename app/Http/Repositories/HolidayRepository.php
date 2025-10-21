<?php

namespace App\Http\Repositories;

use App\Helper\Helper;
use App\Models\Holiday;
use Illuminate\Support\Facades\DB;

class HolidayRepository
{
    private Holiday $holiday;

    public function __construct()
    {
        $this->holiday = new Holiday();
    }

    /**
     * @return mixed
     */
    public function findALl(): mixed
    {
        return $this->holiday->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function save($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return $this->holiday->create($data)->fresh();
        });
    }

    /**
     * @param $holiday
     * @param $data
     * @return mixed
     */
    public function update($holiday, $data): mixed
    {
        return DB::transaction(static function () use ($holiday, $data) {
            return $holiday->update($data);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->holiday->find($id);
    }

    /**
     * @param Holiday $holiday
     * @return mixed
     */
    public function delete(Holiday $holiday): mixed
    {
        return DB::transaction(static function () use ($holiday) {
            return $holiday->delete();
        });
    }

    public function getSelectList(): mixed
    {
        return $this->holiday->where('is_active', true)->pluck('title', 'id');
    }

    public function getCurrentHolidayList()
    {
        $from = now()->subMonths(12);
        $to = now()->addMonths(12);
        return $this->holiday->whereDate('date', '>', $from->format('Y-m-d'))
            ->whereDate('date', '<', $to->format('Y-m-d'))
            ->where('is_active', true)->orderBy('date','asc')->get();
    }
    public function getHolidayByName($holiday_name)
    {
        return $this->holiday->where('title',  trim($holiday_name) )->first();
    }

    public function checkHolidayByDate($date)
    {
        return $this->holiday->whereDate('date', $date)->first();
    }


}
