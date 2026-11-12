<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'organisation' => ['nullable', 'string', 'max:180'],
            'enquiry_type' => ['required', Rule::in(['Partnership', 'Training & mentorship', 'Equipment & programme support', 'Market access', 'Volunteering', 'General enquiry'])],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return ['website.max' => 'Your enquiry could not be submitted. Please email or call us directly.'];
    }
}
