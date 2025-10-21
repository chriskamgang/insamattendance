<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Repositories\ShiftRepository;
use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use JetBrains\PhpStorm\ArrayShape;

class ShiftServices
{
    private string $notFoundMessage = "Sorry! Shift not found";
    private ShiftRepository $shiftRepository;


    public function __construct()
    {
        $this->shiftRepository = new ShiftRepository();
    }

    public function getList()
    {
        return $this->shiftRepository->findALl();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveShift($request)
    {
        $is_early_check_in = $request->is_early_check_in ?? false;
        $is_early_check_out = $request->is_early_check_out ?? false;
        return $this->shiftRepository->save([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'type' => $request->type,
            'is_early_check_in' => $is_early_check_in,
            'is_early_check_out' => $is_early_check_out,
            'before_start' => (($is_early_check_in)?  0 : ($request->before_start ?? 0)),
            'after_start' => $request->after_start,
            'before_end' => (($is_early_check_out)? 0 : ($request->before_end ?? 0)),
            'after_end' => $request->after_end,
            'is_active' => true,
        ]);
    }

    /**
     * @throws SMException
     */
    public function getShift($shift_id)
    {
        $_shift = $this->shiftRepository->find($shift_id);
        if ($_shift) {
            return $_shift;
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updateShift($shift_id, $request)
    {
        $_shift = $this->shiftRepository->find($shift_id);
        if ($_shift) {
            $is_early_check_in = $request->is_early_check_in ?? false;
            $is_early_check_out = $request->is_early_check_out ?? false;
            return $this->shiftRepository->update($_shift, [
                'title' => $request->title,
                'start' => $request->start,
                'end' => $request->end,
                'type' => $request->type,
                'is_early_check_in' => $is_early_check_in,
                'is_early_check_out' => $is_early_check_out,
                'before_start' => (($is_early_check_in)? 0 : $request->before_start),
                'after_start' => $request->after_start,
                'before_end' => (($is_early_check_out)? 0 : $request->before_end),
                'after_end' => $request->after_end,
                'is_active' => true,
            ]);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function deleteShift($shift_id)
    {
        $_shift = $this->shiftRepository->find($shift_id);
        if ($_shift) {
            $_shiftCount = $_shift->getUser()->count();
            if ($_shiftCount == 0){
                $this->shiftRepository->update($_shift, [
                    'title' => $_shift->title . "-" . Helper::smTodayInYmdHis(),
                ]);
                return $this->shiftRepository->delete($_shift);
            }
            throw new SMException("Before deleting the shift, you'll need to unlink it from the employee it's currently associated with.");
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    #[ArrayShape(['success' => "bool", 'message' => "string"])]
    public function changeStatus($user_id): array
    {
        $_shift = $this->shiftRepository->find($user_id);
        if ($_shift) {
            $this->shiftRepository->update($_shift, ['is_active' => (($_shift->is_active == 1) ? 0 : 1)]);
            return ['success' => true, 'message' => 'Status has been updated successfully'];
        }
        throw new SMException($this->notFoundMessage);
    }

    public function getSelectList()
    {
        return $this->shiftRepository->getSelectList();
    }

    public function getShiftApi()
    {
        $_notice = $this->shiftRepository->getActiveShift();
        return ShiftResource::collection($_notice);
    }



}
