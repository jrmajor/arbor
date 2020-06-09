<?php

namespace App\Http\Livewire\Dashboard;

use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Users extends Component
{
    public function render()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $users = User::all()->load('latestLogin')
                    ->sortByDesc(fn ($user) => optional($user->latestLogin)->created_at);

        return view('livewire.dashboard.users', [
            'users' => $users,
        ]);
    }
}
