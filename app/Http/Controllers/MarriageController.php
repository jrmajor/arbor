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
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class MarriageController extends Controller
{
    public function create(Request $request): Response
    {
        $this->authorize('create', Marriage::class);

        return Inertia::render('Marriages/Create', [
            'manId' => $request->integer('man') ?: null,
            'womanId' => $request->integer('woman') ?: null,
        ]);
    }

    public function store(CreateMarriage $request): RedirectResponse
    {
        $this->authorize('create', Marriage::class);

        $marriage = new Marriage($request->validated());

        $marriage->save()
            ? flash('success', 'marriages.alerts.marriage_has_been_created')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    public function edit(Marriage $marriage): Response
    {
        $this->authorize('update', $marriage);

        return Inertia::render('Marriages/Edit', [
            'marriage' => new EditMarriageResource($marriage),
        ]);
    }

    public function update(EditMarriage $request, Marriage $marriage): RedirectResponse
    {
        $this->authorize('update', $marriage);

        $marriage->update($request->validated())
            ? flash('success', 'marriages.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    public function destroy(Marriage $marriage): RedirectResponse
    {
        $this->authorize('delete', $marriage);

        $marriage->delete()
            ? flash('success', 'marriages.alerts.marriage_has_been_deleted')
            : flash('error', 'misc.an_unknown_error_occurred');

        return Auth::user()->canViewHistory()
            ? redirect()->route('marriages.history', $marriage)
            : redirect()->route('people.show', $marriage->woman);
    }

    public function restore(Marriage $marriage): RedirectResponse
    {
        $this->authorize('restore', $marriage);

        $marriage->restore()
            ? flash('success', 'marriages.alerts.marriage_has_been_restored')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    public function history(Marriage $marriage): Response
    {
        $this->authorize('viewHistory', $marriage);

        $activities = $marriage->activities->load('causer')->reverse()->values();

        return Inertia::render('Marriages/History', [
            'marriage' => new MarriagePageResource($marriage),
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
