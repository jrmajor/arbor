<?php

use App\Http\Controllers\SettingsController;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('settings-old', Settings::class)
    ->middleware('auth')->name('settings');

Route::get('settings', [SettingsController::class, 'edit'])
    ->middleware('auth')->name('settings.edit');

Route::put('settings/email', [SettingsController::class, 'updateEmail'])
    ->middleware('auth')->name('settings.updateEmail');

Route::put('settings/password', [SettingsController::class, 'updatePassword'])
    ->middleware('auth')->name('settings.updatePassword');

Route::post('settings/logout-other-devices', [SettingsController::class, 'logoutOtherDevices'])
    ->middleware('auth')->name('settings.logoutOtherDevices');
