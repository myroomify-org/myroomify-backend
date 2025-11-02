<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function update(Request $request) {
        $user = auth()->user();

        $user->email = $request->email;
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'The email address has been updated successfully.',
            'data' => $user
        ], 200);
    }
}
