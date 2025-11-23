<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\EmailUpdateRequest;

class EmailController extends Controller
{
    public function update(EmailUpdateRequest $request) {
        $user = auth()->user();

        $validated = $request->validated();

        $user->email = $validated['email'];

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'The email address has been updated successfully.',
            'data' => $user
        ], 200);
    }
}
