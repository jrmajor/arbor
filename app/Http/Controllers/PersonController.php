<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerson;
use App\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Person::class);

        return view('people.index');
    }

    public function letter($type, $letter)
    {
        $this->authorize('viewAny', Person::class);

        if ($type == 'f') {
            $list = Person
                ::whereRaw('left(family_name, 1) collate utf8mb4_0900_as_ci = ?', $letter)
                ->orderBy('family_name', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        } elseif ($type == 'l') {
            $list = Person
                ::whereRaw('left(ifnull(last_name, family_name), 1) collate utf8mb4_0900_as_ci = ?', $letter)
                ->orderByRaw('ifnull(last_name, family_name) asc')
                ->orderBy('name', 'asc')
                ->get();
        } else {
            abort(404);
        }

        if ($list->isEmpty()) {
            abort(404);
        }

        return view('people.index')
                ->with('active', ['type' => strtolower($type), 'letter' => $letter])
                ->with('list', $list);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Person::class);

        $person = new Person();

        if ($request->input('mother') && $mother = Person::find($request->input('mother'))) {
            $person->mother_id = $mother->id;
        }

        if ($request->input('father') && $father = Person::find($request->input('father'))) {
            $person->father_id = $father->id;
        }

        return view('people.create', ['person' => $person]);
    }

    public function store(StorePerson $request)
    {
        $this->authorize('create', Person::class);

        $person = Person::create($request->validated());

        return redirect()->route('people.show', [$person]);
    }

    public function show(Person $person)
    {
        $this->authorize('view', $person);

        return view('people.person', ['person' => $person]);
    }

    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        return view('people.edit', ['person' => $person]);
    }

    public function update(StorePerson $request, Person $person)
    {
        $this->authorize('update', $person);

        $person->fill($request->validated())->save();

        return redirect()->route('people.show', [$person]);
    }

    public function changeVisibility(Request $request, Person $person)
    {
        $this->authorize('changeVisibility', $person);

        $request->validate([
            'visibility' => 'required|boolean',
        ]);

        if (! $person->changeVisibility($request['visibility'])) {
            return abort(500);
        }

        return redirect()->route('people.show', [$person]);
    }

    public function destroy(Person $person)
    {
        $this->authorize('delete', $person);

        if ($person->marriages->count() > 0) {
            return back()->withErrors([
                'deleting' => __('people.cant_delete_person_with_relationships'),
            ]);
        }

        if ($person->children->count() > 0) {
            return back()->withErrors([
                'deleting' => __('people.cant_delete_person_with_children'),
            ]);
        }

        $person->delete();

        return redirect()->route('people.index');
    }

    public function history(Person $person)
    {
        $this->authorize('viewHistory', $person);

        $activities = $person->activities
            ->reverse()
            ->map(fn ($activity) => [
                'model' => $activity,
                'causer' => $activity->causer,
                'description' => $activity->description,
                'old' => $activity->properties['old'] ?? false,
                'attributes' => $activity->properties['attributes'],
            ]);

        return view('people.history', [
            'person' => $person,
            'activities' => $activities,
        ]);
    }
}
