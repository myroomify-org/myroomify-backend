<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class BookingPolicy {
    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->role === 'superadmin') {
            return Response::allow();
        }
    }

    public function viewAny(User $user) {
        if (in_array($user->role, ['admin', 'receptionist'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function view(User $user, Booking $booking) {
        if (in_array($user->role, ['admin', 'receptionist'])) {
            return Response::allow();
        }

        if ($user->role === 'customer' && $booking->user_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function create(User $user) {
        if (in_array($user->role, ['admin', 'receptionist', 'customer'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function update(User $user, Booking $booking) {
        if (in_array($user->role, ['admin', 'receptionist'])) {
            return Response::allow();
        }

        if ($user->role === 'customer' && $booking->user_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function delete(User $user, Booking $booking) {
        return $this->update($user, $booking);
    }

    public function restore(User $user, Booking $booking) {
        return $this->delete($user, $booking);
    }

    public function forceDelete(User $user, Booking $booking) {
        return Response::deny('You do not have permission to perform this action!');
    }
}
