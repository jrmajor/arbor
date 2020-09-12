<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PersonPickerController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! Auth::user()->canWrite()) {
            abort(403);
        }

        if (blank($request->get('search'))) {
            return response()->json([]);
        }

        $people = Person::
            where(function ($q) use ($request) {
                $q->where('id', $request->get('search'))
                    ->orWhere(function ($q) use ($request) {
                        foreach (Arr::trim(explode(' ', $request->get('search'))) as $s) {
                            $q->where(function ($q) use ($s) {
                                return $q->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%');
                            });
                        }
                    });
            })->when(filled($request->get('sex')), function ($q) use ($request) {
                return $q->where(fn ($q) => $q->where('sex', $request->get('sex'))->orWhereNull('sex'));
            })
            ->limit(10)
            ->get();

        return response()->json(
            $people->map(fn ($person) => ['id' => $person->id, 'name' => $person->formatName()])
        );
    }
}
