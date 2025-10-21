<?php

namespace App\Http\Resources;

use App\Helper\Helper;
use App\Http\Enums\EDateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'description'  => $this->description,
            'start_date'  => Helper::smDate($this->start_date, EDateFormat::Ymd),
            'end_date'  => Helper::smDate($this->end_date, EDateFormat::Ymd),
        ];
    }
}
