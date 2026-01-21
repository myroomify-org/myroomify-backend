<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoomStoreRequest;
use App\Http\Requests\Admin\RoomUpdateRequest;
use App\Models\Room;
use App\Models\RoomImage;

class RoomController extends Controller
{
    public function index() {
        $rooms = Room::withTrashed()->with('primaryImage')->get();

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
        $room = Room::withTrashed()->with('images')->find($id);

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

    public function store(RoomStoreRequest $request) {
        $validated = $request->validated();
        
        $room = new Room();
        $room->name = $validated['name'];
        $room->capacity = $validated['capacity'];
        $room->description = $validated['description'];
        $room->price = $validated['price'];

        $room->save();

        $files = $request->allFiles();

        if (isset($files['images'])) {
            foreach ($files['images'] as $index => $file) {
                $filename = 'room_' . $room->id . '_' . ($index + 1) . '.' . $file->extension();
                $path = $file->storeAs('rooms', $filename, 'public');

                $image = new RoomImage();
                $image->room_id = $room->id;
                $image->path = $path;
                $image->is_primary = $index === 0;
                $image->position = $index;
                $image->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'The new room has been created successfully.',
            'data' => $room
        ], 201);
    }

    public function update(RoomUpdateRequest $request, $id) {
        $room = Room::withTrashed()->find($id);

        if(!$room) {
            return response()->json([
                'success' => false,
                'message' => 'No room found with the given ID.',
                'data' => $room
            ], 404);
        }

        $validated = $request->validated();

        $room->name = $validated['name'];
        $room->capacity = $validated['capacity'];
        $room->description = $validated['description'];
        $room->price = $validated['price'];

        $files = $request->allFiles();

        if (isset($files['images'])) {
            $lastPosition = $room->images->max('position') ?? 0;

            foreach ($files['images'] as $index => $file) {
                $filename = 'room_' . $room->id . '_' . ($lastPosition + $index + 1) . '.' . $file->extension();
                $path = $file->storeAs('rooms', $filename, 'public');

                $image = new RoomImage();
                $image->room_id = $room->id;
                $image->path = $path;
                $image->is_primary = false;
                $image->position = $lastPosition + $index + 1;
                $image->save();
            }
        }

        if (!$room->images->where('is_primary', true)->count() && $room->images->count()) {
            $room->images->first()->update(['is_primary' => true]);
        }

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
