<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocaleRequest;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __invoke(LocaleRequest $request): RedirectResponse
    {
        session(['locale' => $request->locale()]);

        return back();
    }
}
