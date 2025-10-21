<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Repositories\NoticeRepository;
use App\Http\Resources\NoticeResource;
use JetBrains\PhpStorm\ArrayShape;

class NoticeServices
{
    private string $notFoundMessage = "Sorry! Notice not found";
    private NoticeRepository $noticeRepository;


    public function __construct()
    {
        $this->noticeRepository = new NoticeRepository();
    }

    public function getList()
    {
        return $this->noticeRepository->findALl();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveNotice($request)
    {
        return $this->noticeRepository->save([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => true,
        ]);
    }

    /**
     * @throws SMException
     */
    public function getNotice($notice_id)
    {
        $_notice = $this->noticeRepository->find($notice_id);
        if ($_notice) {
            return $_notice;
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updateNotice($notice_id, $request)
    {
        $_notice = $this->noticeRepository->find($notice_id);
        if ($_notice) {
            return $this->noticeRepository->update($_notice, [
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
            ]);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function deleteNotice($notice_id)
    {
        $_notice = $this->noticeRepository->find($notice_id);
        if ($_notice) {
            $this->noticeRepository->update($_notice, [
                'title' => $_notice->title . "-" . Helper::smTodayInYmdHis(),
            ]);
            return $this->noticeRepository->delete($_notice);
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    #[ArrayShape(['success' => "bool", 'message' => "string"])]
    public function changeStatus($user_id): array
    {
        $_notice = $this->noticeRepository->find($user_id);
        if ($_notice) {
            $this->noticeRepository->update($_notice, ['is_active' => (($_notice->is_active == 1) ? 0 : 1)]);
            return ['success' => true, 'message' => 'Status has been updated successfully'];
        }
        throw new SMException($this->notFoundMessage);
    }

    public function getAllNoticeApi()
    {
        $_notice = $this->noticeRepository->getActiveNotice();
        return NoticeResource::collection($_notice);
    }
}
