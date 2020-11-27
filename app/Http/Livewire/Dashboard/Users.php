<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Users extends Component
{
    public function render()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $users = User::all()->load('latestLogin')
            ->sortByDesc(fn ($user) => $user->latestLogin?->created_at);

        return view('dashboard.users', [
            'users' => $users,
        ])->extends('layouts.app');
    }
}
