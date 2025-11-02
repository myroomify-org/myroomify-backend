<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class AuthController extends Controller
{
    public function register(Request $request) {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'customer';
        $user->is_active = true;

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
            'message' => 'Registration was successful.',
            'data' => $user->load('profile.address.city.country')
        ], 201);
    }

    public function login(Request $request) {
        $login = $request->login;
        $password = $request->password;

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
