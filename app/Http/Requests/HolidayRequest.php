<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HolidayRequest extends FormRequest
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
                Rule::unique('holidays')->ignore($this->holiday),
            ],
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title required',
            'title.unique' => 'Title must be unique',
            'date.required' => 'Date required',
        ];
    }
}
