<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarriage;
use App\Models\Activity;
use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function App\Services\flash;

class MarriageController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', Marriage::class);

        $marriage = new Marriage([
            'man_order' => 1,
            'woman_order' => 1,
            'man_id' => Person::find($request->input('man'))?->id,
            'woman_id' => Person::find($request->input('woman'))?->id,
        ]);

        return view('marriages.create', ['marriage' => $marriage]);
    }

    public function store(StoreMarriage $request)
    {
        $this->authorize('create', Marriage::class);

        $marriage = new Marriage($request->validated());

        $marriage->save()
            ? flash('success', 'marriages.alerts.marriage_has_been_created')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    public function edit(Marriage $marriage)
    {
        $this->authorize('update', $marriage);

        return view('marriages.edit', ['marriage' => $marriage]);
    }

    public function update(StoreMarriage $request, Marriage $marriage)
    {
        $this->authorize('update', $marriage);

        $marriage->update($request->validated())
            ? flash('success', 'marriages.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    public function destroy(Marriage $marriage)
    {
        $this->authorize('delete', $marriage);

        $marriage->delete()
            ? flash('success', 'marriages.alerts.marriage_has_been_deleted')
            : flash('error', 'misc.an_unknown_error_occurred');

        return Auth::user()->canViewHistory()
            ? redirect()->route('marriages.history', $marriage)
            : redirect()->route('people.show', $marriage->woman);
    }

    public function restore(Marriage $marriage)
    {
        $this->authorize('restore', $marriage);

        $marriage->restore()
            ? flash('success', 'marriages.alerts.marriage_has_been_restored')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $marriage->woman);
    }

    public function history(Marriage $marriage)
    {
        $this->authorize('viewHistory', $marriage);

        $activities = $marriage->activities
            ->reverse()
            ->map(fn (Activity $activity) => [
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
