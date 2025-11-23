<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\ProfileUpdateRequest;
use App\Models\Profile;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class ProfileController extends Controller
{
    public function show() {
        $user = auth()->user();
        
        return response()->json([
            'success' => true,
            'message' => 'Your profile details have been retrieved successfully.',
            'data' => $user->load('profile.address.city.country')
        ], 200);
    }

    public function update(ProfileUpdateRequest $request) {
        $user = auth()->user();

        $validated = $request->validated();

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

        if($user->profile) {
            $profile = $user->profile;
        } else {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        $profile->first_name = $validated['first_name'];
        $profile->last_name = $validated['last_name'];
        $profile->phone = $validated['phone'];
        $profile->address_id = $address->id;

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Your profile details have been updated successfully.',
            'data' => $user->load('profile.address.city.country')
        ], 200);
    }
}
