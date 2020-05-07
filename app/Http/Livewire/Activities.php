<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Activities extends Component
{
    use WithPagination;

    public function paginationView()
    {
        return 'components.pagination-links';
    }

    public function render()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        return view('livewire.activities', [
            'activities' => Activity::latest()->paginate(10),
        ]);
    }
}
