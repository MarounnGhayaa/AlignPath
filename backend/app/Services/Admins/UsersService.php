<?php

namespace App\Services\Admins;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class UsersService {
    /**
     * Delete a user, enforcing admin-only and "no self-delete" rules.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function deleteUser(User $target, ?User $actor){
        if (!$actor || $actor->role !== 'admin') {
            throw new AuthorizationException('Forbidden');
        }

        if ($actor->id === $target->id) {
            throw ValidationException::withMessages([
                'user' => 'Cannot delete yourself',
            ]);
        }

        $target->delete();
    }
}
