<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Menu extends Component
{
    public ?User $user;

    public ?string $active;

    public function __construct()
    {
        $this->user = Auth::user();

        $this->active = Route::currentRouteName();

        if ($this->active === 'people.letter') {
            $this->active = 'people.index';
        }

        if (Str::startsWith($this->active, 'dashboard')) {
            $this->active = 'dashboard';
        }
    }

    public function render()
    {
        return view('components.menu');
    }
}
