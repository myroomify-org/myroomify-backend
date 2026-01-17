<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BookingGuestStoreRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'nationality' => ['required', 'string', 'max:255'],

            'document_type' => ['required', 'string', 'in:passport,id_card,driver_license'],
            'document_number' => ['required', 'string', 'max:255'],

            'country_name' => ['required', 'string', 'max:255'],
            'city_name' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'birth_date.required' => 'Date of birth is required.',
            'birth_date.before' => 'Date of birth must be in the past.',
            'nationality.required' => 'Nationality is required.',

            'document_type.required' => 'Document type is required.',
            'document_type.in' => 'Invalid document type.',
            'document_number.required' => 'Document number is required.',

            'country_name.required' => 'Country is required.',
            'city_name.required' => 'City is required.',
            'postal_code.required' => 'Postal code is required.',
            'address.required' => 'Address is required.',
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
