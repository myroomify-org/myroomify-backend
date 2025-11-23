<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $validated = $request->validated();

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'customer';
        $user->is_active = true;

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
            'message' => 'Registration was successful.',
            'data' => $user->load('profile.address.city.country')
        ], 201);
    }

    public function login(LoginRequest $request) {
        $validated = $request->validated();

        $login = $validated['login'];
        $password = $validated['password'];

        $emailOrUsername = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if(!Auth::attempt([$emailOrUsername => $login, 'password' => $password])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.',
                'data' => null
            ], 401);
        }

        $user = Auth::user();

        if(!$user->is_active) {
            $user->tokens()->delete();

            return response()->json([
                'success' => false,
                'message' => 'The user account is inactive.',
                'data' => null
            ], 403);
        }

        $token = $user->createToken($user->name . '_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login was successful.',
            'data' => [
                'user' => $user->load('profile.address.city.country'),
                'token' => $token
            ]
        ], 200);
    }

    public function logout() {
        $user = auth('sanctum')->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout was successful.',
            'data' => null
        ], 200);
    }
}
