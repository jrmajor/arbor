<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PeopleSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        if (blank($request->get('search'))) {
            return response()->json([]);
        }

        $people = Person::where(function ($query) use ($request) {
                $query
                    ->where('id', $request->get('search'))
                    ->orWhere(function ($query) use ($request) {
                        foreach (Arr::trim(explode(' ', $request->get('search'))) as $s) {
                            $query->where(function ($query) use ($s) {
                                return $query->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%');
                            });
                        }
                    });
            })
            ->when(filled($request->get('sex')), fn ($query) => $query->where(function ($query) use ($request) {
                $query
                    ->where('sex', $request->get('sex'))
                    ->orWhereNull('sex');
            }))
            ->unless(Auth::user()?->canRead(), fn ($query) => $query->where('visibility', true))
            ->limit(10)
            ->get();

        $response = $people
            ->filter(fn ($person) => $person->canBeViewedBy(Auth::user()))
            ->map(fn ($person) => [
                'id' => $person->id,
                'name' => $person->formatSimpleName(),
                'dates' => $person->formatSimpleDates(),
                'url' => route('people.show', $person),
            ]);

        return response()->json($response);
    }
}
