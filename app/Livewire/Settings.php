<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

use function App\flash;

class Settings extends Component
{
    public string $email;

    public string $password = '';

    public string $password_confirmation = '';

    public string $logout_password = '';

    public function saveEmail(): void
    {
        $this->validate(['email' => ['required', 'email']]);

        Auth::user()->update(['email' => $this->email])
            ? flash('success', 'settings.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');
    }

    public function savePassword(): void
    {
        $this->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);

        Auth::user()->update(['password' => $this->password])
            ? flash('success', 'settings.alerts.password_has_been_changed')
            : flash('error', 'misc.an_unknown_error_occurred');

        $this->fill([
            'password' => '',
            'password_confirmation' => '',
        ]);
    }

    public function logoutOtherDevices(): void
    {
        $this->validate(['logout_password' => ['required']]);

        if (! Hash::check($this->logout_password, Auth::user()->password)) {
            $this->addError('logout_password', __('settings.wrong_password'));

            return;
        }

        Auth::guard()->logoutOtherDevices($this->logout_password)
            ? flash('success', 'settings.alerts.logged_out')
            : flash('error', 'misc.an_unknown_error_occurred');

        $this->logout_password = '';
    }

    public function mount(): void
    {
        $this->email = Auth::user()->email;
    }

    public function render(): View
    {
        return view('livewire.settings', [
            'user' => Auth::user(),
        ])->extends('layouts.app');
    }
}
