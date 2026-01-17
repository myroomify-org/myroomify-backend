<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\BookingGuestStoreRequest;
use App\Http\Requests\Admin\BookingGuestUpdateRequest;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class BookingGuestController extends Controller
{
    public function store(BookingGuestStoreRequest $request, $bookingId) {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'The booking could not be found.',
                'data' => $booking
            ], 404);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Guests cannot be added to the booking in its current status.',
                'data' => $booking
            ], 409);
        }

        if ($booking->guests()->count() >= $booking->guest_count) {
            return response()->json([
                'success' => false,
                'message' => 'The maximum number of guests for this booking has been reached.',
                'data' => $booking
            ], 400);
        }

        $validated = $request->validated();

        $guest = new Guest();
        $guest->booking_id = $booking->id;
        $guest->first_name = $validated['first_name'];
        $guest->last_name = $validated['last_name'];
        $guest->birth_date = $validated['birth_date'];
        $guest->nationality = $validated['nationality'];
        $guest->document_type = $validated['document_type'];
        $guest->document_number = $validated['document_number'];

        $country = Country::where('name', $validated['country_name'])->first();

        if(!$country) {
            $country = new Country();
            $country->name = $validated['country_name'];
            $country->save();
        }

        $city = City::where('name', $validated['city_name'])->where('country_id', $country->id)->first();
        if(!$city) {
            $city = new City();
            $city->name = $validated['city_name'];
            $city->country_id = $country->id;
            $city->save();
        }

        $address = Address::where('postal_code', $validated['postal_code'])->where('address', $validated['address'])->where('city_id', $city->id)->first();

        if(!$address) {
            $address = new Address();
            $address->postal_code = $validated['postal_code'];
            $address->address = $validated['address'];
            $address->city_id = $city->id;
            $address->save();
        }

        $guest->address_id = $address->id;

        $guest->save();

        return response()->json([
            'success' => true,
            'message' => 'The guest details have been saved successfully.',
            'data' => $guest
        ], 201);
    }

    public function update(BookingGuestUpdateRequest $request, $bookingId, $guestId) {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'The booking could not be found.',
                'data' => $booking
            ], 404);
        }

        $guest = $booking->guests()->find($guestId);
        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'The guest could not be found.',
                'data' => $guest
            ], 404);
        }

        $validated = $request->validated();

        $guest->first_name = $validated['first_name'];
        $guest->last_name = $validated['last_name'];
        $guest->birth_date = $validated['birth_date'];
        $guest->nationality = $validated['nationality'];
        $guest->document_type = $validated['document_type'];
        $guest->document_number = $validated['document_number'];

        $country = Country::where('name', $validated['country_name'])->first();

        if(!$country) {
            $country = new Country();
            $country->name = $validated['country_name'];
            $country->save();
        }

        $city = City::where('name', $validated['city_name'])->where('country_id', $country->id)->first();
        if(!$city) {
            $city = new City();
            $city->name = $validated['city_name'];
            $city->country_id = $country->id;
            $city->save();
        }

        $address = Address::where('postal_code', $validated['postal_code'])->where('address', $validated['address'])->where('city_id', $city->id)->first();

        if(!$address) {
            $address = new Address();
            $address->postal_code = $validated['postal_code'];
            $address->address = $validated['address'];
            $address->city_id = $city->id;
            $address->save();
        }

        $guest->address_id = $address->id;

        $guest->save();

        return response()->json([
            'success' => true,
            'message' => 'The guest details have been updated successfully.',
            'data' => $guest
        ], 200);
    }

    public function destroy($bookingId, $guestId) {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'The booking could not be found.',
                'data' => $booking
            ], 404);
        }

        $guest = $booking->guests()->find($guestId);
        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'The guest could not be found.',
                'data' => $guest
            ], 404);
        }

        $guest->delete();

        return response()->json([
            'success' => true,
            'message' => 'The guest has been deleted successfully.',
            'data' => $guest
        ], 200);
    }
}
