<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Psl\Str;
use Psl\Vec;
use Symfony\Component\HttpFoundation\Response;

use function App\trim_values;

class PeopleSearchController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $query = $request->get('search');

        if (blank($query)) {
            return response()->json([]);
        }

        $sex = filled($request->get('sex')) ? $request->get('sex') : null;

        $whereFragment = function (Builder $q) use ($query) {
            $queryFragments = Vec\filter_nulls(trim_values(Str\split($query, ' ')));

            foreach ($queryFragments as $fragment) {
                $q->where(function (Builder $q) use ($fragment) {
                    return $q->whereRaw('name collate utf8mb4_0900_ai_ci like ?', "{$fragment}%")
                        ->orWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', "{$fragment}%")
                        ->orWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', "{$fragment}%");
                });
            }
        };

        $whereSex = function (Builder $q) use ($sex) {
            return $q->where(function (Builder $q) use ($sex) {
                $q->where('sex', $sex)->orWhereNull('sex');
            });
        };

        $people = Person::query()
            ->where(fn (Builder $q) => $q->where('id', $query)->orWhere($whereFragment))
            ->when($sex, $whereSex)
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
