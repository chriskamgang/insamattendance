<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Repositories\LeaveTypeRepository;
use JetBrains\PhpStorm\ArrayShape;

class LeaveTypeServices
{
    private string $notFoundMessage = "Sorry! Leave Type not found";
    private LeaveTypeRepository $leaveTypeRepository;


    public function __construct()
    {
        $this->leaveTypeRepository = new LeaveTypeRepository();
    }

    public function getList()
    {
        return $this->leaveTypeRepository->findALl();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveLeaveType($request)
    {
        return $this->leaveTypeRepository->save([
            'title' => $request->title,
            'allocated_days' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * @throws SMException
     */
    public function getLeaveType($leaveType_id)
    {
        $_leaveType = $this->leaveTypeRepository->find($leaveType_id);
        if ($_leaveType) {
            return $_leaveType;
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updateLeaveType($leaveType_id, $request)
    {
        $_leaveType = $this->leaveTypeRepository->find($leaveType_id);
        if ($_leaveType) {
            return $this->leaveTypeRepository->update($_leaveType, [
                'title' => $request->title,
            ]);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function deleteLeaveType($leaveType_id)
    {
        $_leaveType = $this->leaveTypeRepository->find($leaveType_id);
        if ($_leaveType) {
            $this->leaveTypeRepository->update($_leaveType, [
                'title' => $_leaveType->title . "-" . Helper::smTodayInYmdHis(),
            ]);
            return $this->leaveTypeRepository->delete($_leaveType);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    #[ArrayShape(['success' => "bool", 'message' => "string"])]
    public function changeStatus($user_id): array
    {
        $_leaveType = $this->leaveTypeRepository->find($user_id);
        if ($_leaveType) {
            $this->leaveTypeRepository->update($_leaveType, ['is_active' => (($_leaveType->is_active == 1) ? 0 : 1)]);
            return ['success' => true, 'message' => 'Status has been updated successfully'];
        }
        throw new SMException($this->notFoundMessage);
    }
    public function getSelectList()
    {
        return $this->leaveTypeRepository->getSelectList();
    }
}
