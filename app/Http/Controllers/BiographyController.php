<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiography;
use App\Http\Resources\People\EditBiographyResource;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class BiographyController extends Controller
{
    public function edit(Person $person): Response
    {
        $this->authorize('update', $person);

        return Inertia::render('People/EditBiography', [
            'person' => new EditBiographyResource($person),
        ]);
    }

    public function update(StoreBiography $request, Person $person): RedirectResponse
    {
        $this->authorize('update', $person);

        $person->update(['biography' => $request->biography()])
            ? flash('success', 'people.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }
}
