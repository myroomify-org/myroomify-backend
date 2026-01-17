<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Public\RoomController as PublicRoomController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Me\ProfileController;
use App\Http\Controllers\Api\Me\EmailController;
use App\Http\Controllers\Api\Me\PasswordController;
use App\Http\Controllers\Api\Me\BookingController;
use App\Http\Controllers\Api\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\BookingGuestController as AdminBookingGuestController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('public')->group(function () {
    Route::get('/rooms', [PublicRoomController::class, 'index']);
    Route::get('/rooms/{id}', [PublicRoomController::class, 'show']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('me')->middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/email', [EmailController::class, 'update']);
    Route::put('/password', [PasswordController::class, 'update']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
});

Route::prefix('admin')->group(function () {
    Route::get('/rooms', [AdminRoomController::class, 'index']);
    Route::get('/rooms/{id}', [AdminRoomController::class, 'show']);
    Route::post('/rooms', [AdminRoomController::class, 'store']);
    Route::put('/rooms/{id}', [AdminRoomController::class, 'update']);
    Route::delete('/rooms/{id}', [AdminRoomController::class, 'destroy']);
    Route::post('/rooms/{id}/restore', [AdminRoomController::class, 'restore']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
    Route::post('/users/{id}/restore', [AdminUserController::class, 'restore']);

    Route::get('/bookings', [AdminBookingController::class, 'index']);
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
    Route::post('/bookings', [AdminBookingController::class, 'store']);
    Route::put('/bookings/{id}', [AdminBookingController::class, 'update']);

    Route::post('/bookings/{bookingId}/guests', [AdminBookingGuestController::class, 'store']);
    Route::put('/bookings/{bookingId}/guests/{guestId}', [AdminBookingGuestController::class, 'update']);
    Route::delete('/bookings/{bookingId}/guests/{guestId}', [AdminBookingGuestController::class, 'destroy']);
});
