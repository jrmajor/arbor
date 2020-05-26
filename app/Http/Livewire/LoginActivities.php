<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class LoginActivities extends Component
{
    use WithPagination;

    public $showMine = false;

    public function updatingShowMine()
    {
        $this->resetPage();
    }

    public function render()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $activities = Activity::latest()
            ->with('causer')
            ->where('log_name', 'logins')
            ->when(! $this->showMine, fn ($q) => $q->where('causer_id', '!=', auth()->user()->id))
            ->paginate();

        return view('livewire.login-activities', [
            'activities' => $activities,
        ]);
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }
}
