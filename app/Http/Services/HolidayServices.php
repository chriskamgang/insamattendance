<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Repositories\HolidayRepository;
use App\Http\Resources\HolidayResource;
use JetBrains\PhpStorm\ArrayShape;

class HolidayServices
{
    private string $notFoundMessage = "Sorry! Holiday not found";
    private HolidayRepository $holidayRepository;


    public function __construct()
    {
        $this->holidayRepository = new HolidayRepository();
    }

    public function getList()
    {
        return $this->holidayRepository->findALl();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveHoliday($request)
    {
        return $this->holidayRepository->save([
            'title' => $request->title,
            'date' => $request->date,
            'is_active' => true,
        ]);
    }

    /**
     * @throws SMException
     */
    public function getHoliday($holiday_id)
    {
        $_holiday = $this->holidayRepository->find($holiday_id);
        if ($_holiday) {
            return $_holiday;
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updateHoliday($holiday_id, $request)
    {
        $_holiday = $this->holidayRepository->find($holiday_id);
        if ($_holiday) {
            return $this->holidayRepository->update($_holiday, [
                'title' => $request->title,
                'date' => $request->date,
            ]);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function deleteHoliday($holiday_id)
    {
        $_holiday = $this->holidayRepository->find($holiday_id);
        if ($_holiday) {
            $this->holidayRepository->update($_holiday, [
                'title' => $_holiday->title . "-" . Helper::smTodayInYmdHis(),
            ]);
            return $this->holidayRepository->delete($_holiday);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    #[ArrayShape(['success' => "bool", 'message' => "string"])]
    public function changeStatus($user_id): array
    {
        $_holiday = $this->holidayRepository->find($user_id);
        if ($_holiday) {
            $this->holidayRepository->update($_holiday, ['is_active' => (($_holiday->is_active == 1) ? 0 : 1)]);
            return ['success' => true, 'message' => 'Status has been updated successfully'];
        }
        throw new SMException($this->notFoundMessage);
    }

    public function getCurrentHolidayListApi()
    {
        $_holiday = $this->holidayRepository->getCurrentHolidayList();
        return HolidayResource::collection($_holiday);
    }
}
