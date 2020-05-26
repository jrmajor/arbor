<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    public $email;

    public $password;
    public $password_confirmation;

    public $logout_password;

    public function saveEmail()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        Auth::user()->fill([
            'email' => $this->email,
        ])->save();
    }

    public function savePassword()
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->fill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->password = null;
        $this->password_confirmation = null;
    }

    public function logoutOtherDevices()
    {
        $this->validate([
            'logout_password' => 'required',
        ]);

        if (! Hash::check($this->logout_password, Auth::user()->password)) {
            $this->addError('logout_password', __('settings.wrong_password'));
            return;
        }

        Auth::guard()->logoutOtherDevices($this->logout_password);

        $this->logout_password = null;
    }

    public function mount()
    {
        $this->email = Auth::user()->email;
    }

    public function render()
    {
        return view('livewire.settings', [
            'user' => Auth::user(),
        ]);
    }
}
