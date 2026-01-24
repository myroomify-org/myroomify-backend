<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\BookingStoreRequest;
use App\Http\Requests\Admin\BookingUpdateRequest;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index() {

        $bookings = Booking::with(['user', 'room', 'guests', 'bookingBillingDetail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'All bookings have been retrieved successfully.',
            'data' => $bookings,
        ]);
    }

    public function show($id) {
        $booking = Booking::with(['user', 'room', 'guests', 'bookingBillingDetail'])->find($id);

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

    public function store(BookingStoreRequest $request) {
        $validated = $request->validated();

        $user = User::find($validated['user_id']);
        $room = Room::find($validated['room_id']);

        $overlappingBooking = $room->bookings()
            ->where('check_in', '<', $validated['check_out'])
            ->where('check_out', '>', $validated['check_in'])
            ->first();

        if ($overlappingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This room is already booked for the selected period.',
                'data' => $overlappingBooking
            ], 409);
        }

        if ($validated['guest_count'] > $room->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'The room does not have enough capacity for the specified number of guests.',
                'data' => null
            ], 422);
        }

        $booking = new Booking();
        $booking->user_id = $user->id;
        $booking->room_id = $room->id;
        $booking->check_in = $validated['check_in'];
        $booking->check_out = $validated['check_out'];
        $booking->guest_count = $validated['guest_count'];
        $days = max(1, Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out)));
        $booking->total_price = $room->price * $days * $booking->guest_count;
        $booking->status = $validated['status'];

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'The booking has been created successfully.',
            'data' => $booking->load(['user', 'room', 'guests', 'bookingBillingDetail']),
        ], 201);
    }

    public function update(BookingUpdateRequest $request, $id) {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'No booking found with the given ID.',
                'data' => $booking
            ], 404);
        }

        $room = $booking->room;

        $validated = $request->validated();

        $overlappingBooking = $room->bookings()
            ->where('id', '!=', $booking->id)
            ->where('check_in', '<', $validated['check_out'])
            ->where('check_out', '>', $validated['check_in'])
            ->first();

        if ($overlappingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This room is already booked for the selected period.',
                'data' => $overlappingBooking
            ], 409);
        }

        if ($validated['guest_count'] > $room->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'The room does not have enough capacity for the specified number of guests.',
                'data' => null
            ], 422);
        }

        $booking->check_in = $validated['check_in'];
        $booking->check_out = $validated['check_out'];
        $booking->guest_count = $validated['guest_count'];
        $days = max(1, Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out)));
        $booking->total_price = $room->price * $days * $booking->guest_count;
        $booking->status = $validated['status'];

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'The booking has been updated successfully.',
            'data' => $booking->load(['user', 'room', 'guests', 'bookingBillingDetail']),
        ]);
    }

    public function confirm($id) {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'The booking could not be found.',
                'data' => $booking
            ], 404);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only bookings with a "pending" status can be confirmed.',
                'data' => $booking
            ], 400);
        }

        $booking->status = 'confirmed';

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'The booking has been successfully confirmed.',
            'data' => $booking
        ], 200);
    }

    public function cancel($id) {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'The booking could not be found.',
                'data' => $booking
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'The booking has already been cancelled.',
                'data' => $booking
            ], 400);
        }

        $booking->status = 'cancelled';

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'The booking has been successfully cancelled.',
            'data' => $booking
        ], 200);
    }
}
