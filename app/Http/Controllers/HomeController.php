<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function welcome()
    {
        return view('welcome');
    }
}
