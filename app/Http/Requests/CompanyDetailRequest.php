<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyDetailRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'primary_email' => 'required|string',
            'secondary_email' => 'nullable|email',
            'primary_contact_no' => 'required|numeric',
            'secondary_contact_no' => 'nullable|numeric',
            'address' => 'required|string',
            'website_url' => 'nullable|url',
        ];
    }
}
