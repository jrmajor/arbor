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

    /**
     * @todo allow people deletion
     */
    public function destroy(Person $person)
    {
        // $this->authorize('delete', $person);
    }
}
