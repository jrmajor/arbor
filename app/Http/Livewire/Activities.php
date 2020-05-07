<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Activities extends Component
{
    use WithPagination;

    public bool $filterLogins = true;
    public bool $filterUsers = true;
    public bool $filterEditions = true;

    protected function logsToShow()
    {
        $logs = [];

        if ($this->filterLogins) {
            $logs[] = 'logins';
        }

        if ($this->filterUsers) {
            $logs[] = 'users';
        }

        if ($this->filterEditions) {
            $logs[] = 'people';
            $logs[] = 'marriages';
        }

        return $logs;
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }

    public function render()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $activities = Activity::latest()
            ->whereIn('log_name', $this->logsToShow())
            ->paginate(10);

        return view('livewire.activities', [
            'activities' => $activities,
        ]);
    }
}
