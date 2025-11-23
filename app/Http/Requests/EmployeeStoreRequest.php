<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|' . Rule::unique('employees', 'email')->ignore($this->employee),
            'phone' => 'nullable|string|' . Rule::unique('employees', 'phone')->ignore($this->employee),
        ];
    }
}
