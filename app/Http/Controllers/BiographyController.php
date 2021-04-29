<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiography;
use App\Models\Person;

class BiographyController extends Controller
{
    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        return view('people.biography.edit', ['person' => $person]);
    }

    public function update(StoreBiography $request, Person $person)
    {
        $this->authorize('update', $person);

        $person->update(['biography' => $request->biography()])
            ? flash()->success(__('people.alerts.changes_have_been_saved'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return redirect()->route('people.show', $person);
    }
}
