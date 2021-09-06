<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Users extends Component
{
    public function render(): View
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $users = User::query()->with('latestLogin')->get()
            ->sortByDesc(fn ($user) => $user->latestLogin?->created_at);

        return view('dashboard.users', [
            'users' => $users,
        ])->extends('layouts.app');
    }
}
