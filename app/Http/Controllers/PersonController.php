<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerson;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PersonController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Person::class);

        return view('people.index');
    }

    /**
     * @param 'f'|'l' $type
     */
    public function letter(string $type, string $letter)
    {
        $this->authorize('viewAny', Person::class);

        $list = match ($type) {
            'f' => Person::whereRaw('left(family_name, 1) collate utf8mb4_0900_as_ci = ?', $letter)
                ->orderBy('family_name')
                ->orderBy('name')
                ->get(),
            'l' => Person::whereRaw('left(ifnull(last_name, family_name), 1) collate utf8mb4_0900_as_ci = ?', $letter)
                ->orderByRaw('ifnull(last_name, family_name) asc')
                ->orderBy('name')
                ->get(),
        };

        if ($list->isEmpty()) {
            abort(404);
        }

        return view('people.index', [
            'list' => $list,
            'activeLetter' => $letter,
            'activeType' => mb_strtolower($type),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Person::class);

        $person = new Person();

        if ($request->has('mother') && $mother = Person::find($request->input('mother'))) {
            $person->mother_id = $mother->id;
        }

        if ($request->has('father') && $father = Person::find($request->input('father'))) {
            $person->father_id = $father->id;
        }

        return view('people.create', ['person' => $person]);
    }

    public function store(StorePerson $request)
    {
        $this->authorize('create', Person::class);

        $person = new Person($request->validated());

        $person->save()
            ? flash()->success(__('people.alerts.person_has_been_created'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return redirect()->route('people.show', $person);
    }

    public function show(Person $person)
    {
        if ($person->trashed()) {
            return Auth::user()?->canViewHistory()
                ? redirect()->route('people.history', $person)
                : abort(404);
        }

        $this->authorize('view', $person);

        return view('people.person', [
            'person' => $person,
            'biography' => Str::formatBiography($person->biography),
        ]);
    }

    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        return view('people.edit', ['person' => $person]);
    }

    public function update(StorePerson $request, Person $person)
    {
        $this->authorize('update', $person);

        $person->update($request->validated())
            ? flash()->success(__('people.alerts.changes_have_been_saved'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return redirect()->route('people.show', $person);
    }

    public function changeVisibility(Request $request, Person $person)
    {
        $this->authorize('changeVisibility', $person);

        $visibility = $request->validate([
            'visibility' => 'required|boolean',
        ])['visibility'];

        $person->changeVisibility($visibility)
            ? flash()->success(__('people.alerts.visibility_has_been_changed'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return back();
    }

    public function destroy(Person $person)
    {
        $this->authorize('delete', $person);

        if ($person->marriages->isNotEmpty()) {
            flash()->error(__('people.alerts.cant_delete_person_with_marriages'));

            return back();
        }

        if ($person->children->isNotEmpty()) {
            flash()->error(__('people.alerts.cant_delete_person_with_children'));

            return back();
        }

        $person->delete()
            ? flash()->success(__('people.alerts.person_has_been_deleted'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return Auth::user()->canViewHistory()
            ? redirect()->route('people.history', $person)
            : redirect()->route('people.index');
    }

    public function restore(Person $person)
    {
        $this->authorize('restore', $person);

        $person->restore()
            ? flash()->success(__('people.alerts.person_has_been_restored'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return redirect()->route('people.show', $person);
    }

    public function history(Person $person)
    {
        $this->authorize('viewHistory', $person);

        $activities = $person->activities
            ->reverse()
            ->map(function ($activity) {
                $newActivity = [
                    'model' => $activity,
                    'causer' => $activity->causer,
                    'description' => $activity->description,
                    'old' => $activity->properties['old'] ?? false,
                ];

                if ($activity->properties->has('attributes')) {
                    $newActivity['attributes'] = $activity->properties['attributes'];
                }

                if ($activity->properties->has('new')) {
                    $newActivity['new'] = $activity->properties['new'];
                }

                return $newActivity;
            });

        return view('people.history', [
            'person' => $person,
            'activities' => $activities,
        ]);
    }
}
