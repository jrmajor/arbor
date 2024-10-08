<?php

namespace App\Http\Controllers;

use App\Http\Resources\Dashboard\ActivityResource;
use App\Http\Resources\Dashboard\UserResource;
use App\Http\Resources\People\PersonResource;
use App\Models\Activity;
use App\Models\Person;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

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

    public function users(): Response
    {
        $users = User::query()
            ->with('latestLogin')
            ->get()
            ->sortByDesc(fn ($user) => $user->latestLogin?->created_at);

        return Inertia::render('Dashboard/Users', [
            'users' => UserResource::collection($users),
        ]);
    }

    public function activityLog(Request $request): Response
    {
        $activities = Activity::latest()
            ->with(['causer', 'subject'])
            ->whereIn('log_name', ['users', 'people', 'marriages'])
            ->paginate();

        return Inertia::render(
            'Dashboard/ActivityLog',
            ActivityResource::collection($activities)->toResponse($request)->getData(true),
        );
    }

    public function reports(): Response
    {
        $shouldBeDead = Person::where('dead', false)
            ->where(fn (Builder $q) => $q->whereNotNull([
                'death_date_from', 'death_date_to', 'death_place', 'death_cause',
                'funeral_date_from', 'funeral_date_to', 'funeral_place',
                'burial_date_from', 'burial_date_to', 'burial_place',
            ], 'or'))
            ->get();

        $visibleAlive = Person::where('dead', false)
            ->where('visibility', true)->get();

        $invisibleDead = Person::where('dead', true)
            ->where('visibility', false)->get();

        return Inertia::render('Dashboard/Reports', [
            'shouldBeDead' => PersonResource::collection($shouldBeDead),
            'visibleAlive' => PersonResource::collection($visibleAlive),
            'invisibleDead' => PersonResource::collection($invisibleDead),
        ]);
    }
}
