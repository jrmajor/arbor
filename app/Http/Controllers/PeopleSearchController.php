<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PeopleSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        if (blank($request->get('search'))) {
            return response()->json([]);
        }

        $people = Person::query()
            ->where(function (Builder $q) use ($request) {
                $q->where('id', $request->get('search'))
                    ->orWhere(function (Builder $q) use ($request) {
                        foreach (Arr::trim(explode(' ', $request->get('search'))) as $s) {
                            $q->where(function (Builder $q) use ($s) {
                                return $q->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->orWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->orWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%');
                            });
                        }
                    });
            })
            ->when(filled($request->get('sex')), function (Builder $q) use ($request) {
                return $q->where(function (Builder $q) use ($request) {
                    $q->where('sex', $request->get('sex'))->orWhereNull('sex');
                });
            })
            ->unless(Auth::user()?->canRead(), fn (Builder $q) => $q->where('visibility', true))
            ->limit(10)
            ->get();

        $response = $people
            ->filter(fn (Person $person) => Gate::allows('view', $person))
            ->map(fn (Person $person) => [
                'id' => $person->id,
                'name' => $person->formatSimpleName(),
                'dates' => $person->formatSimpleDates(),
                'url' => route('people.show', $person),
            ]);

        return response()->json($response);
    }
}
