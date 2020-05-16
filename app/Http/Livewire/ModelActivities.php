<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class ModelActivities extends Component
{
    use WithPagination;

    public function render()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $activities = Activity::latest()
            ->with(['causer', 'subject'])
            ->whereIn('log_name', ['users', 'people', 'marriages'])
            ->paginate();

        return view('livewire.model-activities', [
            'activities' => $activities,
        ]);
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }
}
