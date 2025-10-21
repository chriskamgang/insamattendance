<?php

namespace App\Http\Requests;

use App\Models\Shift;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string',
                Rule::unique('shifts')->ignore($this->shift),
            ],
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
            'type' => ['required',Rule::in(Shift::SHIFT)],
            'is_early_check_in' => ['nullable', 'boolean', Rule::in([1, 0])],
            'before_start' => 'nullable|numeric',
            'after_start' => 'required|numeric|min:2',
            'is_early_check_out' => ['nullable', 'boolean', Rule::in([1, 0])],
            'before_end' => 'nullable|numeric',
            'after_end' => 'required|numeric|min:2',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title required',
            'date.required' => 'Date required',
            'description.required' => 'Description required',
        ];
    }
}
