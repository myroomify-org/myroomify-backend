<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\RoomPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class => BookingPolicy::class,
        Room::class => RoomPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
