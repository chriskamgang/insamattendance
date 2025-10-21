<?php

namespace App\Http\Repositories;

use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class ShiftRepository
{
    private Shift $shift;

    public function __construct()
    {
        $this->shift = new Shift();
    }

    /**
     * @return mixed
     */
    public function findALl(): mixed
    {
        return $this->shift->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function save($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return $this->shift->create($data)->fresh();
        });
    }

    /**
     * @param $shift
     * @param $data
     * @return mixed
     */
    public function update($shift, $data): mixed
    {
        return DB::transaction(static function () use ($shift, $data) {
            return $shift->update($data);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->shift->find($id);
    }

    /**
     * @param Shift $shift
     * @return mixed
     */
    public function delete(Shift $shift): mixed
    {
        return DB::transaction(static function () use ($shift) {
            return $shift->delete();
        });
    }

    public function getSelectList(): mixed
    {
        return $this->shift->where('is_active', true)->pluck('title', 'id');
    }

    public function getActiveShift()
    {
        return $this->shift->where('is_active', true)->get();
    }

    public function getLeaveTypeByName($shift_name)
    {
        return $this->shift->where('title', trim($shift_name))->first();
    }
}
