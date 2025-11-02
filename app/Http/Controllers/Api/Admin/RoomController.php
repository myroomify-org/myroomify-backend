<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index() {
        $rooms = Room::withTrashed()->get();

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
        $room = Room::withTrashed()->find($id);

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

    public function store(Request $request) {
        $room = new Room();
        $room->name = $request->name;
        $room->capacity = $request->capacity;
        $room->description = $request->description;
        $room->price = $request->price;

        $room->save();

        if($request->hasFile('image')) {
            $filename = 'szobakep_' . $room->id . '.' . $request->file('image')->extension();
            $path = $request->file('image')->storeAs('rooms', $filename, 'public');
            $room->image = $path;
        }else {
            $room->image = null;
        }

        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'The new room has been created successfully.',
            'data' => $room
        ], 201);
    }

    public function update(Request $request, $id) {
        $room = Room::withTrashed()->find($id);

        if(!$room) {
            return response()->json([
                'success' => false,
                'message' => 'No room found with the given ID.',
                'data' => $room
            ], 404);
        }

        $room->name = $request->name;
        $room->capacity = $request->capacity;
        $room->description = $request->description;
        $room->price = $request->price;

        $files = $request->allFiles();

        if(isset($files['image'])) {
            $filename = 'szobakep_' . $room->id . '.' . $request->file('image')->extension();
            $path = $files['image']->storeAs('rooms', $filename, 'public');
            $room->image = $path;
        }

        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'The room details have been updated successfully.',
            'data' => $room
        ], 200);
    }

    public function destroy($id) {
        $room = Room::withTrashed()->find($id);

        if(!$room) {
            return response()->json([
                'success' => false,
                'message' => 'No room found with the given ID.',
                'data' => $room
            ], 404);
        }

        if($room->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'The room has already been deleted.',
                'data' => $room
            ], 400);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'The room has been deleted successfully.',
            'data' => $room
        ], 200);
    }

    public function restore($id) {
        $room = Room::withTrashed()->find($id);

        if(!$room) {
            return response()->json([
                'success' => false,
                'message' => 'No room found with the given ID.',
                'data' => $room
            ], 404);
        }

        if(!$room->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'The room is not deleted, therefore it cannot be restored.',
                'data' => $room
            ], 400);
        }

        $room->restore();

        return response()->json([
            'success' => true,
            'message' => 'The room has been restored successfully.',
            'data' => $room
        ], 200);
    }
}
