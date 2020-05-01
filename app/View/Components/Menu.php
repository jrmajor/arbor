<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Menu extends Component
{
    public $user;

    public function __construct()
    {
        $this->user = optional(Auth::user());
    }

    public function render()
    {
        return view('components.menu');
    }
}
