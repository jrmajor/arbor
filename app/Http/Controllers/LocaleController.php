<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocaleRequest;
use Symfony\Component\HttpFoundation\Response;

class LocaleController extends Controller
{
    public function __invoke(LocaleRequest $request): Response
    {
        session(['locale' => $request->locale()]);

        return back();
    }
}
