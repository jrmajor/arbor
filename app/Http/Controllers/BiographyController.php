<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBiography;
use App\Http\Resources\People\EditBiographyResource;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class BiographyController extends Controller
{
    #[Authorize('update', 'person')]
    public function edit(Person $person): Response
    {
        return Inertia::render('People/EditBiography', [
            'person' => new EditBiographyResource($person),
        ]);
    }

    #[Authorize('update', 'person')]
    public function update(StoreBiography $request, Person $person): RedirectResponse
    {
        $person->update(['biography' => $request->biography()])
            ? flash('success', 'people.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');

        return redirect()->route('people.show', $person);
    }
}
