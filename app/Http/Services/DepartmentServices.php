<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Repositories\DepartmentRepository;
use App\Http\Resources\DepartmentResource;
use JetBrains\PhpStorm\ArrayShape;

class DepartmentServices
{
    private string $notFoundMessage = "Sorry! Department not found";
    private DepartmentRepository $departmentRepository;


    public function __construct()
    {
        $this->departmentRepository = new DepartmentRepository();
    }

    public function getList()
    {
        return $this->departmentRepository->findALl();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveDepartment($request)
    {
        return $this->departmentRepository->save([
            'title' => $request->title,
            'is_active' => true,
        ]);
    }

    /**
     * @throws SMException
     */
    public function getDepartment($department_id)
    {
        $_department = $this->departmentRepository->find($department_id);
        if ($_department) {
            return $_department;
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updateDepartment($department_id, $request)
    {
        $_department = $this->departmentRepository->find($department_id);
        if ($_department) {
            return $this->departmentRepository->update($_department, [
                'title' => $request->title,
            ]);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function deleteDepartment($department_id)
    {
        $_department = $this->departmentRepository->find($department_id);
        if ($_department) {
            $this->departmentRepository->update($_department, [
                'title' => $_department->title . "-" . Helper::smTodayInYmdHis(),
            ]);
            return $this->departmentRepository->delete($_department);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    #[ArrayShape(['success' => "bool", 'message' => "string"])]
    public function changeStatus($user_id): array
    {
        $_department = $this->departmentRepository->find($user_id);
        if ($_department) {
            $this->departmentRepository->update($_department, ['is_active' => (($_department->is_active == 1) ? 0 : 1)]);
            return ['success' => true, 'message' => 'Status has been updated successfully'];
        }
        throw new SMException($this->notFoundMessage);
    }
    public function getSelectList()
    {
        return $this->departmentRepository->getSelectList();
    }

    public function getDepartmenttApi()
    {
        $_notice = $this->departmentRepository->getActiveDepartment();
        return DepartmentResource::collection($_notice);
    }
}
