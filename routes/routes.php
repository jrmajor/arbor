<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/', 'people')
    ->name('welcome');

Route::post('locale', LocaleController::class)
    ->name('locale.store');

Route::get('dashboard/users', [DashboardController::class, 'users'])
    ->middleware('auth')->name('dashboard.users');

Route::get('dashboard/activity-log', [DashboardController::class, 'activityLog'])
    ->middleware('auth')->name('dashboard.activityLog');

Route::get('dashboard/reports', [DashboardController::class, 'reports'])
    ->middleware('auth')->name('dashboard.reports');

require __DIR__ . '/auth.php';

require __DIR__ . '/settings.php';

require __DIR__ . '/people.php';

require __DIR__ . '/marriages.php';

Route::get('sitemap.xml', SitemapController::class)
    ->name('sitemap');

if (app()->isLocal()) {
    require __DIR__ . '/dev.php';
}
