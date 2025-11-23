<?php

namespace App\Http\Requests\Me;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'country_name' => 'required|string|max:255',
            'city_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'country_name.required' => 'The country is required.',
            'city_name.required' => 'The city is required.',
            'postal_code.required' => 'The postal code is required.',
            'address.required' => 'The address is required.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'data'  => $validator->errors()
        ], 422));
    }
}
