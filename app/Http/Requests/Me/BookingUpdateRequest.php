<?php

namespace App\Http\Requests\Me;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BookingUpdateRequest extends FormRequest
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
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1',
        ];
    }

    public function messages(): array {
        return [
            'check_in.required' => 'Check-in date is required.',
            'check_in.date' => 'Check-in date must be a valid date.',
            'check_in.after_or_equal' => 'Check-in date must be today or a future date.',

            'check_out.required' => 'Check-out date is required.',
            'check_out.date' => 'Check-out date must be a valid date.',
            'check_out.after' => 'Check-out date must be after the check-in date.',

            'guest_count.required' => 'Guest count is required.',
            'guest_count.integer' => 'Guest count must be an integer.',
            'guest_count.min' => 'Guest count must be at least 1.',
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
