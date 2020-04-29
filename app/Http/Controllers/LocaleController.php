<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LocaleController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, ['language' => [
            'required',
            'in:' . implode(',', config('app.available_locales')),
        ]]);

        Session::put('locale', $request->input('language'));

        return back();
    }
}
