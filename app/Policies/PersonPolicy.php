<?php

namespace App\Policies;

use App\Person;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Person $person)
    {
        return $person->canBeViewedBy($user);
    }

    public function create(User $user)
    {
        return $user->canWrite();
    }

    public function update(User $user, Person $person)
    {
        return $user->canWrite();
    }

    public function changeVisibility(User $user, Person $person)
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Person $person)
    {
        return $user->canWrite();
    }

    public function restore(User $user, Person $person)
    {
        return $user->canViewHistory();
    }

    public function forceDelete(User $user, Person $person)
    {
        return $user->isSuperAdmin();
    }

    public function viewHistory(User $user, Person $person)
    {
        return $user->canViewHistory();
    }
}
