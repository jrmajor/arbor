<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function reports()
    {
        if (! Auth::user()->isSuperAdmin()) {
            return abort(403);
        }

        $shouldBeDead = Person::where('dead', false)
            ->where(function (Builder $q) {
                return $q->whereNotNull([
                    'death_date_from', 'death_date_to', 'death_place', 'death_cause',
                    'funeral_date_from', 'funeral_date_to', 'funeral_place',
                    'burial_date_from', 'burial_date_to', 'burial_place',
                ], 'or');
            })->get();

        $visibleAlive = Person::where('dead', false)
            ->where('visibility', true)->get();

        $invisibleDead = Person::where('dead', true)
            ->where('visibility', false)->get();

        return view(
            'dashboard.reports',
            compact('shouldBeDead', 'visibleAlive', 'invisibleDead'),
        );
    }
}
