<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy {
    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if ($user->role === 'superadmin' && $ability !== 'delete') {
            return Response::allow();
        }
    }

    public function viewAny(User $user) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function view(User $user, User $model) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function create(User $user) {
        if ($user->role === 'admin') {
            return Response::allow();
        }

        return Response::deny("You do not have permission to perform this action!");
    }

    public function update(User $user, User $model) {
        if ($user->role === 'admin') {
            if(in_array($model->role, ['customer', 'receptionist'])) {
                return Response::allow();
            }
            return Response::deny('You can only modify customer and receptionist users!');
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function delete(User $user, User $model) {
        if ($user->id === $model->id) {
            return Response::deny('You cannot delete yourself!');
        }

        if ($user->role === 'superadmin') {
            return Response::allow();
        }

        if ($user->role === 'admin') {
            if(in_array($model->role, ['customer', 'receptionist'])) {
                return Response::allow();
            }
            return Response::deny('You can only delete customer and receptionist users!');
        }

        return Response::deny('You do not have permission to perform this action!');
    }

    public function restore(User $user, User $model) {
        return $this->delete($user, $model);
    }

    public function forceDelete(User $user, User $model) {
        return Response::deny('You do not have permission to perform this action!');
    }
}
