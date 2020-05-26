<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    public $email;

    public $password;
    public $password_confirmation;

    public function submit()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'nullable|string|confirmed|min:8',
        ]);

        $user = auth()->user();

        $user->email = $this->email;

        if ($this->password != '') {
            $user->password = Hash::make($this->password);
        }

        $user->save();
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
