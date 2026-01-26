<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RoomPolicy {
    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->role === 'superadmin') {
            return Response::allow();
        }
    }

    public function viewAny(User $user) {
        return Response::allow();
    }

    public function view(User $user, Room $room) {
        return Response::allow();
    }

    public function create(User $user) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function update(User $user, Room $room) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function delete(User $user, Room $room) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function restore(User $user, Room $room) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function forceDelete(User $user, Room $room) {
        return Response::deny('You do not have permission to perform this action!');
    }
}
