<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocaleRequest;

class LocaleController extends Controller
{
    public function __invoke(LocaleRequest $request): void
    {
        session(['locale' => $request->locale()]);
    }
}
