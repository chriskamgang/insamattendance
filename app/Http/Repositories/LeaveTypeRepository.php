<?php

namespace App\Http\Repositories;

use App\Models\LeaveTypes;
use Illuminate\Support\Facades\DB;

class LeaveTypeRepository
{
    private LeaveTypes $leaveType;

    public function __construct()
    {
        $this->leaveType = new LeaveTypes();
    }

    /**
     * @return mixed
     */
    public function findALl(): mixed
    {
        return $this->leaveType->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function save($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return $this->leaveType->create($data)->fresh();
        });
    }

    /**
     * @param $leaveType
     * @param $data
     * @return mixed
     */
    public function update($leaveType, $data): mixed
    {
        return DB::transaction(static function () use ($leaveType, $data) {
            return $leaveType->update($data);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->leaveType->find($id);
    }

    /**
     * @param LeaveTypes $leaveType
     * @return mixed
     */
    public function delete(LeaveTypes $leaveType): mixed
    {
        return DB::transaction(static function () use ($leaveType) {
            return $leaveType->delete();
        });
    }

    public function getSelectList(): mixed
    {
        return $this->leaveType->where('is_active', true)->pluck('title', 'id');
    }

    public function getActiveLeaveType()
    {
        return $this->leaveType->where('is_active', true)->get();
    }

    public function getLeaveTypeByName($leaveType_name)
    {
        return $this->leaveType->where('title', trim($leaveType_name))->first();
    }
}
