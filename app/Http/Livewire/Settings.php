<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    public $success;

    public $email;

    public $password;
    public $password_confirmation;

    public $logout_password;

    public function saveEmail()
    {
        $this->success = null;

        $this->validate([
            'email' => 'required|email',
        ]);

        $result = Auth::user()->fill([
            'email' => $this->email,
        ])->save();

        if ($result) {
            $this->success = 'email';
        } else {
            $this->addError('email', __('misc.an_unknown_error_occurred'));
        }
    }

    public function savePassword()
    {
        $this->success = null;

        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = Auth::user()->fill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->password = null;
        $this->password_confirmation = null;

        if ($result) {
            $this->success = 'password';
        } else {
            $this->addError('password', __('misc.an_unknown_error_occurred'));
        }
    }

    public function logoutOtherDevices()
    {
        $this->success = null;

        $this->validate([
            'logout_password' => 'required',
        ]);

        if (! Hash::check($this->logout_password, Auth::user()->password)) {
            $this->addError('logout_password', __('settings.wrong_password'));
            return;
        }

        $result = Auth::guard()->logoutOtherDevices($this->logout_password);

        $this->logout_password = null;

        if ($result) {
            $this->success = 'logout';
        } else {
            $this->addError('logout', __('misc.an_unknown_error_occurred'));
        }
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
