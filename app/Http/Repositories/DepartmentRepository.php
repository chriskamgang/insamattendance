<?php

namespace App\Http\Repositories;

use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentRepository
{
    private Department $department;

    public function __construct()
    {
        $this->department = new Department();
    }

    /**
     * @return mixed
     */
    public function findALl(): mixed
    {
        return $this->department->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function save($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return $this->department->create($data)->fresh();
        });
    }

    /**
     * @param $department
     * @param $data
     * @return mixed
     */
    public function update($department, $data): mixed
    {
        return DB::transaction(static function () use ($department, $data) {
            return $department->update($data);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->department->find($id);
    }

    /**
     * @param Department $department
     * @return mixed
     */
    public function delete(Department $department): mixed
    {
        return DB::transaction(static function () use ($department) {
            return $department->delete();
        });
    }

    public function getSelectList(): mixed
    {
        return $this->department->where('is_active', true)->pluck('title', 'id');
    }

    public function getActiveDepartment()
    {
        return $this->department->where('is_active', true)->get();
    }

    public function getDepartmentByName($department_name)
    {
        return $this->department->where('title', trim($department_name))->first();
    }
}
