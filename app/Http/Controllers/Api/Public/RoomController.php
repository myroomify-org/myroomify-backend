<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index() {
        $rooms = Room::with('primaryImage')->get();

        if($rooms->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'There are currently no rooms registered.',
                'data' => $rooms
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'All rooms have been retrieved successfully.',
            'data' => $rooms
        ], 200);
    }

    public function show($id) {
        $room = Room::with('images')->find($id);

        if(!$room) {
            return response()->json([
                'success' => false,
                'message' => 'No room found with the given ID.',
                'data' => $room
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'The room details have been retrieved successfully.',
            'data' => $room
        ], 200);
    }
}
