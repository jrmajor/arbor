<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarriage;
use App\Marriage;
use App\Person;
use Illuminate\Http\Request;

class MarriageController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', Marriage::class);

        $marriage = new Marriage();

        if ($request->input('woman') && $woman = Person::find($request->input('woman'))) {
            $marriage->woman_id = $woman->id;
        }

        if ($request->input('man') && $man = Person::find($request->input('man'))) {
            $marriage->man_id = $man->id;
        }

        return view('marriages.create', ['marriage' => $marriage]);
    }

    public function store(StoreMarriage $request)
    {
        $this->authorize('create', Marriage::class);

        $marriage = Marriage::create($request->validated());

        return redirect()->route('people.show', [$marriage->woman_id]);
    }

    public function edit(Marriage $marriage)
    {
        $this->authorize('update', $marriage);

        return view('marriages.edit', ['marriage' => $marriage]);
    }

    public function update(StoreMarriage $request, Marriage $marriage)
    {
        $this->authorize('update', $marriage);

        $marriage->fill($request->validated())->save();

        return redirect()->route('people.show', [$marriage->woman_id]);
    }

    public function destroy(Marriage $marriage)
    {
        $this->authorize('delete', $marriage);

        $marriage->delete();

        return redirect()->route('people.show', ['person' => $marriage->woman_id]);
    }

    public function history(Marriage $marriage)
    {
        $this->authorize('viewHistory', $marriage);

        $activities = $marriage->activities
            ->reverse()
            ->map(fn ($activity) => [
                'model' => $activity,
                'causer' => $activity->causer,
                'description' => $activity->description,
                'old' => $activity->properties['old'] ?? false,
                'attributes' => $activity->properties['attributes'],
            ]);

        return view('marriages.history', [
            'marriage' => $marriage,
            'activities' => $activities,
        ]);
    }
}
