<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('people.index'))->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
});
Route::post('logout', 'Auth\LogoutController')->name('logout');

/**
 * @todo allow users edition
 */
// Route::get('users', 'UsersController@index')->name('users.index');
// Route::get('users/create', 'UsersController@create')->name('users.create');
// Route::post('users', 'UsersController@store')->name('users.store');
// Route::get('users/{user}', 'UsersController@show')->name('users.show');
// Route::get('users/{user}/edit', 'UsersController@edit')->name('users.edit');
// Route::match(['put', 'patch'], 'users/{user}', 'UsersController@update')->name('users.update');
// Route::delete('users/{user}', 'UsersController@destroy')->name('users.destroy');

Route::livewire('search', 'search')->name('search');

Route::livewire('settings', 'settings')->name('settings')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('people/create', 'PersonController@create')->name('people.create');
    Route::post('people', 'PersonController@store')->name('people.store');
    Route::get('people/{person}/edit', 'PersonController@edit')->name('people.edit');
    Route::match(['put', 'patch'], 'people/{person}', 'PersonController@update')->name('people.update');
    Route::put('people/{person}/visibility', 'PersonController@changeVisibility')->name('people.changeVisibility');
    Route::delete('people/{person}', 'PersonController@destroy')->name('people.destroy');
    Route::patch('people/{trashedPerson}/restore', 'PersonController@restore')->name('people.restore');
    Route::get('people/{anyPerson}/history', 'PersonController@history')->name('people.history');
    Route::get('people/picker', 'PersonPickerController')->name('people.picker');
});
Route::get('people', 'PersonController@index')->name('people.index');
Route::get('people/{type}/{letter}', 'PersonController@letter')->where('type', '[fl]')->name('people.letter');
Route::get('people/{anyPerson}', 'PersonController@show')->name('people.show');

Route::middleware('auth')->group(function () {
    Route::get('marriages/create', 'MarriageController@create')->name('marriages.create');
    Route::post('marriages', 'MarriageController@store')->name('marriages.store');
    Route::get('marriages/{marriage}/edit', 'MarriageController@edit')->name('marriages.edit');
    Route::match(['put', 'patch'], 'marriages/{marriage}', 'MarriageController@update')->name('marriages.update');
    Route::delete('marriages/{marriage}', 'MarriageController@destroy')->name('marriages.destroy');
    Route::patch('marriages/{trashedMarriage}/restore', 'MarriageController@restore')->name('marriages.restore');
    Route::get('marriages/{anyMarriage}/history', 'MarriageController@history')->name('marriages.history');
});

Route::middleware('auth')->group(function () {
    Route::livewire('dashboard/users', 'dashboard.users')->name('dashboard.users');
    Route::livewire('dashboard/activitylog', 'dashboard.activity-log')->name('dashboard.activitylog');
    Route::get('dashboard/reports', 'DashboardController@reports')->name('dashboard.reports');
});

Route::post('locale', 'LocaleController')->name('locale.set');
