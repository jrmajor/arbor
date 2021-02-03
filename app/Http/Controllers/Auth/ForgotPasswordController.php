<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected function sendResetLinkResponse(Request $request, $response)
    {
        flash()->success(trans($response));

        return redirect()->route('people.index');
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        flash()->error(__($response));

        return redirect()->route('people.index');
    }
}
