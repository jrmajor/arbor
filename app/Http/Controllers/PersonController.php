<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerson;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\People\EditPersonResource;
use App\Http\Resources\People\PersonPageResource;
use App\Http\Resources\People\PersonResource;
use App\Http\Resources\People\ShowPersonResource;
use App\Models\Person;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class PersonController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Person::class);

        return Inertia::render('People/Index', [
            'total' => Person::count(),
            'letters' => [
                'family' => Person::letters('family'),
                'last' => Person::letters('last'),
            ],
        ]);
    }

    /**
     * @param 'f'|'l' $type
     */
    public function letter(Request $request, string $type, string $letter): Response
    {
        $this->authorize('viewAny', Person::class);

        $people = match ($type) {
            'f' => Person::query()
                ->whereRaw('substr(family_name, 1, 1) /* collate utf8mb4_0900_as_ci */ = ?', $letter)
                ->orderBy('family_name')->orderBy('name')->get(),
            'l' => Person::query()
                ->whereRaw('substr(ifnull(last_name, family_name), 1, 1) /* collate utf8mb4_0900_as_ci */ = ?', $letter)
                ->orderByRaw('ifnull(last_name, family_name) asc')->orderBy('name')->get(),
        };

        if ($people->isEmpty()) {
            abort(404);
        }

        return Inertia::render('People/Letter', [
            'people' => PersonResource::collection($people)->toResponse($request)->getData(true),
            'letters' => [
                'family' => Person::letters('family'),
                'last' => Person::letters('last'),
            ],
            'activeType' => $type,
            'activeLetter' => $letter,
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Person::class);

        return Inertia::render('People/Create', [
            'fatherId' => $request->integer('father') ?: null,
            'motherId' => $request->integer('mother') ?: null,
        ]);
    }

    public function store(StorePerson $request): RedirectResponse
    {
        $this->authorize('create', Person::class);

        $person = new Person($request->validated());

        $person->save()
            ? flash('success', 'people.alerts.person_has_been_created')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }

    public function show(Person $person): View|Response|RedirectResponse
    {
        if ($person->trashed()) {
            return Auth::user()?->canViewHistory()
                ? redirect()->route('people.history', $person)
                : abort(404);
        }

        $this->authorize('view', $person);

        return Inertia::render('People/Show', [
            'person' => new ShowPersonResource($person),
        ]);
    }

    public function edit(Person $person): View|Response
    {
        $this->authorize('update', $person);

        return Inertia::render('People/Edit', [
            'person' => new EditPersonResource($person),
        ]);
    }

    public function update(StorePerson $request, Person $person): RedirectResponse
    {
        $this->authorize('update', $person);

        $person->update($request->validated())
            ? flash('success', 'people.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }

    public function changeVisibility(Request $request, Person $person): RedirectResponse
    {
        $this->authorize('changeVisibility', $person);

        $visibility = $request->validate([
            'visibility' => ['required', 'boolean'],
        ]);

        $person->forceFill($visibility)->save()
            ? flash('success', 'people.alerts.visibility_has_been_changed')
            : flash('error', 'misc.an_unknown_error_occurred');

        return back();
    }

    public function destroy(Person $person): RedirectResponse
    {
        $this->authorize('delete', $person);

        if ($person->marriages->isNotEmpty()) {
            flash('error', 'people.alerts.cant_delete_person_with_marriages');

            return back();
        }

        if ($person->children->isNotEmpty()) {
            flash('error', 'people.alerts.cant_delete_person_with_children');

            return back();
        }

        $person->delete()
            ? flash('success', 'people.alerts.person_has_been_deleted')
            : flash('error', 'misc.an_unknown_error_occurred');

        return Auth::user()->canViewHistory()
            ? redirect()->route('people.history', $person)
            : redirect()->route('people.index');
    }

    public function restore(Person $person): RedirectResponse
    {
        $this->authorize('restore', $person);

        $person->restore()
            ? flash('success', 'people.alerts.person_has_been_restored')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }

    public function history(Person $person): Response
    {
        $this->authorize('viewHistory', $person);

        $activities = $person->activities->load('causer')->reverse()->values();

        return Inertia::render('People/History', [
            'person' => new PersonPageResource($person),
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
