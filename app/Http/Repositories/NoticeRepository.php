<?php

namespace App\Http\Repositories;

use App\Models\Notice;
use Illuminate\Support\Facades\DB;

class NoticeRepository
{
    private Notice $notice;

    public function __construct()
    {
        $this->notice = new Notice();
    }

    /**
     * @return mixed
     */
    public function findALl(): mixed
    {
        return $this->notice->orderBy('id', 'desc')->paginate(10);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function save($data): mixed
    {
        return DB::transaction(function () use ($data) {
            return $this->notice->create($data)->fresh();
        });
    }

    /**
     * @param $notice
     * @param $data
     * @return mixed
     */
    public function update($notice, $data): mixed
    {
        return DB::transaction(static function () use ($notice, $data) {
            return $notice->update($data);
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return $this->notice->find($id);
    }

    /**
     * @param Notice $notice
     * @return mixed
     */
    public function delete(Notice $notice): mixed
    {
        return DB::transaction(static function () use ($notice) {
            return $notice->delete();
        });
    }

    public function getSelectList(): mixed
    {
        return $this->notice->where('is_active', true)->pluck('title', 'id');
    }

    public function getActiveNotice()
    {
        return $this->notice->where('is_active', true)->get();
    }
    public function getNoticeByName($notice_name)
    {
        return $this->notice->where('title',  trim($notice_name) )->first();
    }


}
