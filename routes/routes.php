<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@welcome')->name('welcome');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

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

Route::middleware('auth')->group(function () {
    Route::get('people/create', 'PersonController@create')->name('people.create');
    Route::post('people', 'PersonController@store')->name('people.store');
    Route::get('people/{person}/edit', 'PersonController@edit')->name('people.edit');
    Route::match(['put', 'patch'], 'people/{person}', 'PersonController@update')->name('people.update');
    Route::delete('people/{person}', 'PersonController@destroy')->name('people.destroy');
    Route::get('people/{maybe_trashed_person}/history', 'PersonController@history')->name('people.history');
});
Route::get('people', 'PersonController@index')->name('people.index');
Route::get('people/{type}/{letter}', 'PersonController@letter')->where('type', '[fl]')->name('people.letter');
Route::get('people/{person}', 'PersonController@show')->name('people.show');

Route::middleware('auth')->group(function () {
    Route::get('marriages/create', 'MarriageController@create')->name('marriages.create');
    Route::post('marriages', 'MarriageController@store')->name('marriages.store');
    Route::get('marriages/{marriage}/edit', 'MarriageController@edit')->name('marriages.edit');
    Route::match(['put', 'patch'], 'marriages/{marriage}', 'MarriageController@update')->name('marriages.update');
    Route::delete('marriages/{marriage}', 'MarriageController@destroy')->name('marriages.destroy');
    Route::get('marriages/{maybe_trashed_marriage}/history', 'MarriageController@history')->name('marriages.history');
});

Route::livewire('activities', 'activities')->name('activities.index')->middleware('auth');

Route::post('locale', 'LocaleController')->name('locale.set');
