<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            if (! Auth::user()->isSuperAdmin()) {
                return abort(403);
            }

            return $next($request);
        });
    }

    public function reports(): View
    {
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
