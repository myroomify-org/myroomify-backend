<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index() {
        $user = auth()->user();

        $bookings = $user->bookings()->with(['room', 'guests', 'bookingBillingDetail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Your bookings have been retrieved successfully.',
            'data' => $bookings,
        ]);
    }

    public function show($id) {
        $booking = auth()->user()->bookings()->with(['room', 'guests', 'bookingBillingDetail'])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the given ID.',
                'data' => $booking
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'The booking details have been retrieved successfully.',
            'data' => $booking,
        ]);
    }

    public function store(Request $request) {
        $user = auth()->user();

        $room = Room::find($request->room_id);
        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'No room found with the given ID.',
                'data' => $room
            ], 404);
        }

        $overlappingBooking = $room->bookings()
            ->where('check_in', '<', $request->check_out)
            ->where('check_out', '>', $request->check_in)
            ->first();

        if ($overlappingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This room is already booked for the selected period.',
                'data' => $overlappingBooking
            ], 409);
        }

        if ($request->guest_count > $room->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'The room does not have enough capacity for the specified number of guests.',
                'data' => $room
            ], 422);
        }

        $booking = $user->bookings()->make();
        $booking->room_id = $request->room_id;
        $booking->check_in = $request->check_in;
        $booking->check_out = $request->check_out;
        $booking->guest_count = $request->guest_count;
        $days = max(1, Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out)));
        $booking->total_price = $room->price * $days * $booking->guest_count;
        $booking->status = 'pending';
        
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'The booking was successful.',
            'data' => $booking->load(['room', 'guests', 'bookingBillingDetail']),
        ], 201);
    }

    public function update(Request $request, $bookingId) {
        $user = auth()->user();

        $booking = $user->bookings()->find($bookingId);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the given ID.',
                'data' => $booking
            ], 404);
        }

        if (in_array($booking->status, ['completed', 'cancelled', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be modified.',
                'data' => $booking
            ], 403);
        }

        $room = $booking->room;

        $overlappingBooking = $room->bookings()
            ->where('id', '!=', $booking->id)
            ->where('check_in', '<', $request->check_out)
            ->where('check_out', '>', $request->check_in)
            ->first();

        if ($overlappingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This room is already booked for the selected period.',
                'data' => $overlappingBooking
            ], 409);
        }

        if ($request->guest_count > $room->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'The room does not have enough capacity for the specified number of guests.',
                'data' => $room
            ], 422);
        }

        $booking->check_in = $request->check_in;
        $booking->check_out = $request->check_out;
        $booking->guest_count = $request->guest_count;
        $days = max(1, Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out)));
        $booking->total_price = $room->price * $days * $booking->guest_count;

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'The booking has been updated successfully.',
            'data' => $booking->load(['room', 'guests', 'bookingBillingDetail']),
        ]);
    }

}
