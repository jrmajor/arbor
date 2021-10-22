<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;

    public function render(): View
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $activities = Activity::latest()
            ->with(['causer', 'subject'])
            ->whereIn('log_name', ['users', 'people', 'marriages'])
            ->paginate();

        return view('dashboard.activity-log', [
            'activities' => $activities,
        ])->extends('layouts.app');
    }

    public function paginationView(): string
    {
        return 'components.pagination-links';
    }
}
