<?php

namespace App\Policies;

use App\Models\Marriage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarriagePolicy
{
    use HandlesAuthorization;

    public function view(?User $user, Marriage $marriage): bool
    {
        return $user?->canRead() || $marriage->isVisible();
    }

    public function create(User $user): bool
    {
        return $user->canWrite();
    }

    public function update(User $user, Marriage $marriage): bool
    {
        return $user->canWrite();
    }

    public function delete(User $user, Marriage $marriage): bool
    {
        return $user->canWrite();
    }

    public function restore(User $user, Marriage $marriage): bool
    {
        return $user->canViewHistory();
    }

    public function forceDelete(User $user, Marriage $marriage): bool
    {
        return $user->isSuperAdmin();
    }

    public function viewHistory(User $user, Marriage $marriage): bool
    {
        return $user->canViewHistory();
    }
}
