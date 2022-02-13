<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiography;
use App\Models\Person;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

use function App\flash;

class BiographyController extends Controller
{
    public function edit(Person $person): View
    {
        $this->authorize('update', $person);

        return view('people.biography.edit', ['person' => $person]);
    }

    public function update(StoreBiography $request, Person $person): Response
    {
        $this->authorize('update', $person);

        $person->update(['biography' => $request->biography()])
            ? flash('success', 'people.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }
}
