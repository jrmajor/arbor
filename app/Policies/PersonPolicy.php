<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Person $person): bool
    {
        return $user?->canRead() || $person->isVisible();
    }

    public function create(User $user): bool
    {
        return $user->canWrite();
    }

    public function update(User $user, Person $person): bool
    {
        return $user->canWrite();
    }

    public function changeVisibility(User $user, Person $person): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Person $person): bool
    {
        return $user->canWrite();
    }

    public function restore(User $user, Person $person): bool
    {
        return $user->canViewHistory();
    }

    public function forceDelete(User $user, Person $person): bool
    {
        return $user->isSuperAdmin();
    }

    public function viewHistory(User $user, Person $person): bool
    {
        return $user->canViewHistory();
    }
}
