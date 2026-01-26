<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class UserController extends Controller
{
    public function index() {
        Gate::authorize('viewAny', User::class);

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

        Gate::authorize('view', $user);

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

    public function store(UserStoreRequest $request) {
        Gate::authorize('create', User::class);

        $validated = $request->validated();

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->is_active = $validated['is_active'];

        $user->save();

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

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->first_name = $validated['first_name'];
        $profile->last_name = $validated['last_name'];
        $profile->phone = $validated['phone'];
        $profile->address_id = $address->id;

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'The new user has been created successfully.',
            'data' => $user->load('profile.address.city.country')
        ], 201);
    }

    public function update(UserUpdateRequest $request, $id) {
        $user = User::withTrashed()->with('profile.address.city.country')->find($id);

        Gate::authorize('update', $user);

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with the given ID.',
                'data' => $user
            ], 404);
        }

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if($request->has('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->role = $validated['role'];
        $user->is_active = $validated['is_active'];

        $user->save();

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

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'The user details have been updated successfully.',
            'data' => $user
        ], 200);
    }

    public function destroy($id) {
        $user = User::withTrashed()->with('profile.address.city.country')->find($id);

        Gate::authorize('delete', $user);

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

        Gate::authorize('restore', $user);

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
