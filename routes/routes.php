<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MarriageController;
use App\Http\Controllers\PeopleSearchController;
use App\Http\Controllers\PersonController;
use App\Http\Livewire\Dashboard\ActivityLog;
use App\Http\Livewire\Dashboard\Users;
use App\Http\Livewire\Search;
use App\Http\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('people.index'))->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// @todo allow users edition
// Route::get('users', 'UsersController@index')->name('users.index');
// Route::get('users/create', 'UsersController@create')->name('users.create');
// Route::post('users', 'UsersController@store')->name('users.store');
// Route::get('users/{user}', 'UsersController@show')->name('users.show');
// Route::get('users/{user}/edit', 'UsersController@edit')->name('users.edit');
// Route::match(['put', 'patch'], 'users/{user}', 'UsersController@update')->name('users.update');
// Route::delete('users/{user}', 'UsersController@destroy')->name('users.destroy');

Route::get('search', Search::class)->name('search');

Route::get('settings', Settings::class)->name('settings')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('people/create', [PersonController::class, 'create'])->name('people.create');
    Route::post('people', [PersonController::class, 'store'])->name('people.store');
    Route::get('people/{person}/edit', [PersonController::class, 'edit'])->name('people.edit');
    Route::match(['put', 'patch'], 'people/{person}', [PersonController::class, 'update'])->name('people.update');
    Route::delete('people/{person}', [PersonController::class, 'destroy'])->name('people.destroy');
    Route::patch('people/{trashedPerson}/restore', [PersonController::class, 'restore'])->name('people.restore');
    Route::get('people/{anyPerson}/history', [PersonController::class, 'history'])->name('people.history');

    Route::put('people/{person}/visibility', [PersonController::class, 'changeVisibility'])->name('people.changeVisibility');

    Route::get('people/{person}/biography', [BiographyController::class, 'edit'])->name('people.biography.edit');
    Route::patch('people/{person}/biography', [BiographyController::class, 'update'])->name('people.biography.update');
});

Route::get('people/search', PeopleSearchController::class)->name('people.search');
Route::get('people', [PersonController::class, 'index'])->name('people.index');
Route::get('people/{type}/{letter}', [PersonController::class, 'letter'])->where('type', '[fl]')->name('people.letter');
Route::get('people/{anyPerson}', [PersonController::class, 'show'])->name('people.show');

Route::middleware('auth')->group(function () {
    Route::get('marriages/create', [MarriageController::class, 'create'])->name('marriages.create');
    Route::post('marriages', [MarriageController::class, 'store'])->name('marriages.store');
    Route::get('marriages/{marriage}/edit', [MarriageController::class, 'edit'])->name('marriages.edit');
    Route::match(['put', 'patch'], 'marriages/{marriage}', [MarriageController::class, 'update'])->name('marriages.update');
    Route::delete('marriages/{marriage}', [MarriageController::class, 'destroy'])->name('marriages.destroy');
    Route::patch('marriages/{trashedMarriage}/restore', [MarriageController::class, 'restore'])->name('marriages.restore');
    Route::get('marriages/{anyMarriage}/history', [MarriageController::class, 'history'])->name('marriages.history');
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard/users', Users::class)->name('dashboard.users');
    Route::get('dashboard/activitylog', ActivityLog::class)->name('dashboard.activitylog');
    Route::get('dashboard/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
});

Route::post('locale', LocaleController::class)->name('locale.set');
