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

        $result = Auth::user()->fill([
            'email' => $this->email,
        ])->save();

        if ($result) {
            flash()->success(__('settings.alerts.changes_have_been_saved'));
        } else {
            flash()->error(__('misc.an_unknown_error_occurred'));
        }
    }

    public function savePassword()
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = Auth::user()->fill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->password = null;
        $this->password_confirmation = null;

        if ($result) {
            flash()->success(__('settings.alerts.password_has_been_changed'));
        } else {
            flash()->error(__('misc.an_unknown_error_occurred'));
        }
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

        $result = Auth::guard()->logoutOtherDevices($this->logout_password);

        $this->logout_password = null;

        if ($result) {
            flash()->success(__('settings.alerts.logged_out'));
        } else {
            flash()->error(__('misc.an_unknown_error_occurred'));
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
        ])->extends('layouts.app');
    }
}
