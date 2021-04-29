<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocaleRequest;

class LocaleController extends Controller
{
    public function __invoke(LocaleRequest $request)
    {
        session(['locale' => $request->locale()]);

        return back();
    }
}
