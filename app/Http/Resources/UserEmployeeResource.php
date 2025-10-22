<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dob' => $this->dob,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'image' => ($this->image)? asset('/uploads/user/'.$this->image) : "",
            'shift_id' => $this->shift_id,
            'department_id' => $this->department_id,
            'shift_type' => $this->getShift->type,
            'shift_name' => $this->getShift->title,
            'department_name' => $this->getDepartment->title,
        ];
    }
}
