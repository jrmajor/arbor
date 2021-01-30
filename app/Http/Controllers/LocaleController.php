<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function __invoke(Request $request)
    {
        $locale = $this->validate($request, ['language' => [
            'required',
            'in:'.implode(',', config('app.available_locales')),
        ]])['language'];

        Session::put('locale', $locale);

        return back();
    }
}
