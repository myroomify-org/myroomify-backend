<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class UserController extends Controller
{
    public function index() {
        $users = User::withTrashed()->with('profile.address.city.country')->get();

        if($users->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'There are currently no registered users.',
                'data' => $users
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'All users have been retrieved successfully.',
            'data' => $users
        ], 200);
    }

    public function show($id) {
        $user = User::withTrashed()->with('profile.address.city.country')->find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with the given ID.',
                'data' => $user
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'The user details have been retrieved successfully.',
            'data' => $user
        ], 200);
    }

    public function store(Request $request) {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->is_active = $request->is_active;

        $user->save();

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

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->phone = $request->phone;
        $profile->address_id = $address->id;

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'The new user has been created successfully.',
            'data' => $user->load('profile.address.city.country')
        ], 201);
    }

    public function update(Request $request, $id) {
        $user = User::withTrashed()->with('profile.address.city.country')->find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with the given ID.',
                'data' => $user
            ], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->role = $request->role;
        $user->is_active = $request->is_active;

        $user->save();

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

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'The user details have been updated successfully.',
            'data' => $user
        ], 200);
    }

    public function destroy($id) {
        $user = User::withTrashed()->with('profile.address.city.country')->find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with the given ID.',
                'data' => $user
            ], 404);
        }

        if($user->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'The user has already been deleted.',
                'data' => $user
            ], 400);
        }

        if($user->profile) {
            $user->profile->delete();
        }

        $user->delete();

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'The user has been deleted successfully.',
            'data' => $user
        ], 200);
    }

    public function restore($id) {
        $user = User::withTrashed()->with('profile.address.city.country')->find($id);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with the given ID.',
                'data' => $user
            ], 404);
        }

        if(!$user->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'The user is not deleted, therefore it cannot be restored.',
                'data' => $user
            ], 400);
        }

        if($user->profile) {
            $user->profile->restore();
        }

        $user->restore();

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'The user has been restored successfully.',
            'data' => $user
        ], 200);
    }
}
