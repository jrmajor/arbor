<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

use function App\Services\flash;

class Settings extends Component
{
    public $email;

    public $password;

    public $password_confirmation;

    public $logout_password;

    public function saveEmail()
    {
        $this->validate(['email' => 'required|email']);

        Auth::user()->update(['email' => $this->email])
            ? flash('success', 'settings.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');
    }

    public function savePassword()
    {
        $this->validate(['password' => 'required|string|min:8|confirmed']);

        Auth::user()->update(['password' => Hash::make($this->password)])
            ? flash('success', 'settings.alerts.password_has_been_changed')
            : flash('error', 'misc.an_unknown_error_occurred');

        $this->fill([
            'password' => null,
            'password_confirmation' => null,
        ]);
    }

    public function logoutOtherDevices()
    {
        $this->validate(['logout_password' => 'required']);

        if (! Hash::check($this->logout_password, Auth::user()->password)) {
            $this->addError('logout_password', __('settings.wrong_password'));

            return;
        }

        Auth::guard()->logoutOtherDevices($this->logout_password)
            ? flash('success', 'settings.alerts.logged_out')
            : flash('error', 'misc.an_unknown_error_occurred');

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
        ])->extends('layouts.app');
    }
}
