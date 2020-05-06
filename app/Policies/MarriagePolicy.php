<?php

namespace App\Policies;

use App\Marriage;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarriagePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->canWrite();
    }

    public function update(User $user, Marriage $marriage)
    {
        return $user->canWrite();
    }

    public function delete(User $user, Marriage $marriage)
    {
        return $user->canDestroy();
    }

    public function restore(User $user, Marriage $marriage)
    {
        return $user->canDestroy();
    }

    public function forceDelete(User $user, Marriage $marriage)
    {
        return $user->isSuperAdmin();
    }

    public function viewHistory(User $user, Marriage $marriage)
    {
        return $user->isSuperAdmin();
    }
}
