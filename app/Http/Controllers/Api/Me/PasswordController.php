<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request) {
        $user = auth()->user();

        if(!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The current password does not match the existing password',
                'data' => $user
            ], 400);
        }

        $user->password = Hash::make($request->new_password);

        $user->save();

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. Please log in again.',
            'data' => $user->load('profile.address.city.country')
        ], 200);
    }
}
