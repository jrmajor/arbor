<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMarriage;
use App\Http\Requests\EditMarriage;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\Marriages\EditMarriageResource;
use App\Http\Resources\Marriages\MarriagePageResource;
use App\Models\Marriage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class MarriageController extends Controller
{
    #[Authorize('create', Marriage::class)]
    public function create(Request $request): Response
    {
        return Inertia::render('Marriages/Create', [
            'manId' => $request->integer('man', strict: true) ?: null,
            'womanId' => $request->integer('woman', strict: true) ?: null,
        ]);
    }

    #[Authorize('create', Marriage::class)]
    public function store(CreateMarriage $request): RedirectResponse
    {
        $marriage = new Marriage($request->validated());

        $marriage->save()
            ? flash('success', 'marriages.alerts.marriage_has_been_created')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    #[Authorize('update', 'marriage')]
    public function edit(Marriage $marriage): Response
    {
        return Inertia::render('Marriages/Edit', [
            'marriage' => new EditMarriageResource($marriage),
        ]);
    }

    #[Authorize('update', 'marriage')]
    public function update(EditMarriage $request, Marriage $marriage): RedirectResponse
    {
        $marriage->update($request->validated())
            ? flash('success', 'marriages.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    #[Authorize('delete', 'marriage')]
    public function destroy(Marriage $marriage): RedirectResponse
    {
        $marriage->delete()
            ? flash('success', 'marriages.alerts.marriage_has_been_deleted')
            : flash('error', 'misc.an_unknown_error_occurred');

        return Auth::user()->canViewHistory()
            ? redirect()->route('marriages.history', $marriage)
            : redirect()->route('people.show', $marriage->woman);
    }

    #[Authorize('restore', 'trashedMarriage')]
    public function restore(Marriage $marriage): RedirectResponse
    {
        $marriage->restore()
            ? flash('success', 'marriages.alerts.marriage_has_been_restored')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    #[Authorize('viewHistory', 'anyMarriage')]
    public function history(Marriage $marriage): Response
    {
        $activities = $marriage->activitiesAsSubject()->with('causer')->get()->reverse();

        return Inertia::render('Marriages/History', [
            'marriage' => new MarriagePageResource($marriage),
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
