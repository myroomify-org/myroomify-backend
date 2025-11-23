<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:users,name,' . $this->route('id'),
            'email' => 'required|email|max:255|unique:users,email,' . $this->route('id'),
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:customer,receptionist,admin,superadmin',
            'is_active' => 'required|boolean',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'country_name' => 'required|string|max:255',
            'city_name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'The username is required.',
            'name.unique' => 'This username is already taken.',

            'email.required' => 'The email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already taken.',

            'password.min' => 'The password must be at least :min characters.',
            'password.confirmed' => 'The password confirmation does not match.',

            'role.required' => 'The role is required.',
            'role.in' => 'The role value is invalid.',

            'is_active.required' => 'The active status is required.',
            'is_active.boolean' => 'The active status value is invalid.',
            
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'country_name.required' => 'The country is required.',
            'city_name.required' => 'The city is required.',
            'postal_code.required' => 'The postal code is required.',
            'address.required' => 'The address is required.',
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'data' => $validator->errors()
        ], 422));
    }
}
