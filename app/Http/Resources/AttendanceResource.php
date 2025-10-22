<?php

namespace App\Http\Resources;

use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => (int)$this->user_id,
            'name' => $this->name,
            'date' => $this->date ?? Helper::smTodayInYmd(),
            'check_in' => $this->check_in ?? "-",
            'check_out' => $this->check_out ?? "-",
            'lunch_in' => $this->lunch_in ?? "-",
            'lunch_out' => $this->lunch_out ?? "-",
            'check_in_image' => ($this->check_in_image && $this->check_in_image != "-")? asset('/uploads/attendance/'.$this->check_in_image) : "",
            'check_out_image' => ($this->check_out_image && $this->check_out_image != "-")? asset('/uploads/attendance/'.$this->check_out_image) : "",
            'lunch_in_image' => ($this->lunch_in_image && $this->lunch_in_image != "-")? asset('/uploads/attendance/'.$this->lunch_in_image) : "",
            'lunch_out_image' => ($this->lunch_out_image && $this->lunch_out_image != "-")? asset('/uploads/attendance/'.$this->lunch_out_image) : "",
            'is_on_leave' => (int)($this->is_on_leave ?? 0),
            'attendance_id' => (int)($this->attendance_id ?? 0),
            'shift_type' => $this->shift_type,
        ];
    }
}
