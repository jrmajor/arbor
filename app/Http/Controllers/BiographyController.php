<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class BiographyController extends Controller
{
    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        return view('people.biography.edit', ['person' => $person]);
    }

    public function update(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $person->biography = $request->validate([
            'biography' => 'string|max:10000|nullable',
        ])['biography'];

        $person->save()
            ? flash()->success(__('people.alerts.changes_have_been_saved'))
            : flash()->error(__('misc.an_unknown_error_occurred'));

        return redirect()->route('people.show', $person);
    }
}
