<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Dashboard\ActivityLog;
use App\Livewire\Dashboard\Users;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/', 'people')
    ->name('welcome');

Route::redirect('search', 'people');

Route::post('locale', LocaleController::class)
    ->name('locale.store');

Route::get('settings', Settings::class)
    ->middleware('auth')->name('settings');

Route::get('dashboard/users', Users::class)
    ->middleware('auth')->name('dashboard.users');

Route::get('dashboard/activity-log', ActivityLog::class)
    ->middleware('auth')->name('dashboard.activityLog');

Route::get('dashboard/reports', [DashboardController::class, 'reports'])
    ->middleware('auth')->name('dashboard.reports');

require __DIR__ . '/auth.php';

require __DIR__ . '/people.php';

require __DIR__ . '/marriages.php';

Route::get('sitemap.xml', SitemapController::class)
    ->name('sitemap');

if (app()->isLocal()) {
    require __DIR__ . '/dev.php';
}
