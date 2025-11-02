<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function update(Request $request) {
        $user = auth()->user();

        $country = Country::where('name', $request->country_name)->first();

        if(!$country) {
            $country = new Country();
            $country->name = $request->country_name;
            $country->save();
        }

        $city = City::where('name', $request->city_name)->where('country_id', $country->id)->first();

        if(!$city) {
            $city = new City();
            $city->name = $request->city_name;
            $city->country_id = $country->id;
            $city->save();
        }

        $address = Address::where('postal_code', $request->postal_code)->where('address', $request->address)->where('city_id', $city->id)->first();

        if(!$address) {
            $address = new Address();
            $address->postal_code = $request->postal_code;
            $address->address = $request->address;
            $address->city_id = $city->id;
            $address->save();
        }

        if($user->profile) {
            $profile = $user->profile;
        } else {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->phone = $request->phone;
        $profile->address_id = $address->id;

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Your profile details have been updated successfully.',
            'data' => $user->load('profile.address.city.country')
        ], 200);
    }
}
