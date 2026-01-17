<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BookingGuestUpdateRequest extends FormRequest
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
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'nationality' => ['sometimes', 'string', 'max:255'],

            'document_type' => ['sometimes', 'string', 'in:passport,id_card,driver_license'],
            'document_number' => ['sometimes', 'string', 'max:255'],

            'country_name' => ['sometimes', 'string', 'max:255'],
            'city_name' => ['sometimes', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array {
        return [
            'birth_date.before' => 'Date of birth must be in the past.',
            'document_type.in' => 'Invalid document type.',
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'data'  => $validator->errors()
        ], 422));
    }
}
