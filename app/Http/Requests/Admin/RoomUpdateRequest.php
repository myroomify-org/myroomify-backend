<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RoomUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:rooms,name,' . $this->route('id'),
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'Room name is required.',
            'name.unique' => 'This room name already exists.',

            'capacity.required' => 'Room capacity is required.',
            'capacity.integer' => 'Room capacity must be an integer.',
            'capacity.min' => 'Room capacity must be at least 1.',

            'price.required' => 'Room price is required.',
            'price.numeric' => 'Room price must be a number.',
            'price.min' => 'Room price cannot be negative.',

            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be jpeg, png, jpg, gif, or webp.',
            'image.max' => 'Image size must not exceed 2 MB.',
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
