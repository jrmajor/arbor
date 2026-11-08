<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerson;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\People\EditPersonResource;
use App\Http\Resources\People\PersonPageResource;
use App\Http\Resources\People\PersonResource;
use App\Http\Resources\People\ShowPersonResource;
use App\Models\Person;
use App\Services\Pytlewski\PytlewskiScraper;
use App\Services\Wielcy\WielcyScraper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class PersonController extends Controller
{
    #[Authorize('viewAny', Person::class)]
    public function index(): Response
    {
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
    #[Authorize('viewAny', Person::class)]
    public function letter(Request $request, string $type, string $letter): Response
    {
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

    #[Authorize('create', Person::class)]
    public function create(Request $request): Response
    {
        $fatherId = $request->integer('father', strict: true) ?: null;

        return Inertia::render('People/Create', [
            'fatherId' => $fatherId,
            'motherId' => $request->integer('mother', strict: true) ?: null,
            'familyName' => $fatherId
                ? Person::find($fatherId)?->family_name
                : null,
        ]);
    }

    #[Authorize('create', Person::class)]
    public function store(StorePerson $request): RedirectResponse
    {
        $person = new Person($request->validated());

        $person->save()
            ? flash('success', 'people.alerts.person_has_been_created')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }

    public function show(
        Person $person,
        PytlewskiScraper $pytlewskiScraper,
        WielcyScraper $wielcyScraper,
    ): View|Response|RedirectResponse {
        if ($person->trashed()) {
            return Auth::user()?->canViewHistory()
                ? redirect()->route('people.history', $person)
                : abort(404);
        }

        $this->authorize('view', $person);

        $person->loadMissing(['marriages.man', 'marriages.woman']);

        return Inertia::render('People/Show', [
            'person' => new ShowPersonResource(
                $person,
                pytlewski: $pytlewskiScraper->for($person),
                wielcy: $wielcyScraper->for($person),
            ),
        ]);
    }

    #[Authorize('update', 'person')]
    public function edit(Person $person): View|Response
    {
        return Inertia::render('People/Edit', [
            'person' => new EditPersonResource($person),
        ]);
    }

    #[Authorize('update', 'person')]
    public function update(StorePerson $request, Person $person): RedirectResponse
    {
        $person->update($request->validated())
            ? flash('success', 'people.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }

    #[Authorize('changeVisibility', 'person')]
    public function changeVisibility(Request $request, Person $person): RedirectResponse
    {
        $visibility = $request->validate([
            'visibility' => ['required', 'boolean'],
        ]);

        $person->forceFill($visibility)->save()
            ? flash('success', 'people.alerts.visibility_has_been_changed')
            : flash('error', 'misc.an_unknown_error_occurred');

        return back();
    }

    #[Authorize('delete', 'person')]
    public function destroy(Person $person): RedirectResponse
    {
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

    #[Authorize('restore', 'trashedPerson')]
    public function restore(Person $person): RedirectResponse
    {
        $person->restore()
            ? flash('success', 'people.alerts.person_has_been_restored')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }

    #[Authorize('viewHistory', 'anyPerson')]
    public function history(Person $person): Response
    {
        $activities = $person->activitiesAsSubject()->with('causer')->get()->reverse();

        return Inertia::render('People/History', [
            'person' => new PersonPageResource($person),
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
