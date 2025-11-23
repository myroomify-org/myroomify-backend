<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\PasswordUpdateRequest;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(PasswordUpdateRequest $request) {
        $user = auth()->user();

        $validated = $request->validated();

        if(!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The current password does not match the existing password.',
                'data' => null
            ], 400);
        }

        $user->password = Hash::make($validated['new_password']);

        $user->save();

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. Please log in again.',
            'data' => $user->load('profile.address.city.country')
        ], 200);
    }
}
