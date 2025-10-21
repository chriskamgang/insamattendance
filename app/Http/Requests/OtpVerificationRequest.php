<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OtpVerificationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'two_factor_code' => 'required|numeric|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'two_factor_code.required' => 'Otp required',
            'two_factor_code.numeric' => 'Otp must be Numeric',
            'two_factor_code.digits' => 'Otp must be 6 digit',
        ];
    }
}
