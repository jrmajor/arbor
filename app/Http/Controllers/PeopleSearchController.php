<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
            return response()->json(['people' => [], 'hiddenCount' => 0]);
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
            ->limit(10)
            ->get();

        $people = $people->map(fn (Person $p) => Gate::allows('view', $p) ? $p : null);

        $hidden = $people->filter(fn (?Person $p) => $p === null);
        $people = $people->filter(fn (?Person $p) => $p !== null)->values();

        return response()->json([
            'people' => $people->map(fn (Person $p) => [
                'id' => $p->id,
                'name' => $p->formatSimpleName(),
                'dates' => $p->formatSimpleDates(),
                'url' => route('people.show', $p),
            ]),
            'hiddenCount' => $hidden->count(),
        ]);
    }
}
