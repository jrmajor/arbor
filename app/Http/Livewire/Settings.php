<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    public $email;

    public $password;
    public $password_confirmation;

    public function saveEmail()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        auth()->user()->fill([
            'email' => $this->email,
        ])->save();
    }

    public function savePassword()
    {
        $this->validate([
            'password' => 'required|string|confirmed|min:8',
        ]);

        auth()->user()->fill([
            'password' => Hash::make($this->password),
        ])->save();
    }

    public function mount()
    {
        $this->email = auth()->user()->email;
    }

    public function render()
    {
        return view('livewire.settings', [
            'user' => auth()->user(),
        ]);
    }
}
