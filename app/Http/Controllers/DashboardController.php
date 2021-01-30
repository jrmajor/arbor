<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function reports()
    {
        if (! Auth::user()->isSuperAdmin()) {
            return abort(403);
        }

        $shouldBeDead = Person::where('dead', false)
            ->where(function ($q) {
                return $q->whereNotNull('death_date_from')
                    ->orWhereNotNull('death_date_to')
                    ->orWhereNotNull('death_place')
                    ->orWhereNotNull('death_cause')
                    ->orWhereNotNull('funeral_date_from')
                    ->orWhereNotNull('funeral_date_to')
                    ->orWhereNotNull('funeral_place')
                    ->orWhereNotNull('burial_date_from')
                    ->orWhereNotNull('burial_date_to')
                    ->orWhereNotNull('burial_place');
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
