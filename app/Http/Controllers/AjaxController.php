<?php

namespace App\Http\Controllers;

use App\Person;
use App\Services\Pytlewski\Pytlewski;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AjaxController extends Controller
{
    public function person(Request $request)
    {
        $search = $request->input('search');
        $sex = $request->input('sex');

        $people = Person::
            where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere(function ($query) use ($search) {
                        foreach(Arr::trim(explode(' ', $search)) as $s) {
                            $query->where(fn($query) =>
                                $query->whereRaw('name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('family_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                                    ->OrWhereRaw('last_name collate utf8mb4_0900_ai_ci like ?', $s.'%')
                            );
                        }
                    });
            })->when(filled($sex), fn($query) =>
                $query->where(fn($query) =>
                    $query->where('sex', $sex)->orWhereNull('sex')
                )
            )
            ->limit(10)
            ->get();

        $names = [];
        foreach ($people as $person) {
            $names[] = ['id' => $person->id, 'name' => $person->formatName()];
        }

        return response()->json($names);
    }

    public function pytlewski(Request $request)
    {
        $pytlewski = new Pytlewski($request->input('search'));

        if (! $pytlewski->family_name && ! $pytlewski->last_name) {
            return response()->json([
                'name' => __('people.pytlewski.not_found'),
            ]);
        }

        return response()->json([
            'name' => strip_tags($pytlewski->basic_name),
        ]);
    }
}
