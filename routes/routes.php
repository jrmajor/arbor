<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Livewire\Dashboard\ActivityLog;
use App\Http\Livewire\Dashboard\Users;
use App\Http\Livewire\Search;
use App\Http\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/', 'people')
    ->name('welcome');

Route::post('locale', LocaleController::class)
    ->name('locale.store');

Route::get('search', Search::class)
    ->name('search');

Route::get('settings', Settings::class)
    ->middleware('auth')->name('settings');

Route::get('dashboard/users', Users::class)
    ->middleware('auth')->name('dashboard.users');

Route::get('dashboard/activitylog', ActivityLog::class)
    ->middleware('auth')->name('dashboard.activitylog');

Route::get('dashboard/reports', [DashboardController::class, 'reports'])
    ->middleware('auth')->name('dashboard.reports');

require __DIR__.'/auth.php';

require __DIR__.'/people.php';

require __DIR__.'/marriages.php';
