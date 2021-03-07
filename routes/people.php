<?php

use App\Http\Controllers\BiographyController;
use App\Http\Controllers\PeopleSearchController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::get('people', [PersonController::class, 'index'])
    ->name('people.index');

Route::get('people/{type}/{letter}', [PersonController::class, 'letter'])->where('type', '[fl]')
    ->name('people.letter');

Route::get('people/search', PeopleSearchController::class)
    ->name('people.search');

Route::get('people/create', [PersonController::class, 'create'])
    ->middleware('auth')->name('people.create');

Route::post('people', [PersonController::class, 'store'])
    ->middleware('auth')->name('people.store');

Route::get('people/{anyPerson}', [PersonController::class, 'show'])
    ->name('people.show');

Route::get('people/{person}/edit', [PersonController::class, 'edit'])
    ->middleware('auth')->name('people.edit');

Route::match(['put', 'patch'], 'people/{person}', [PersonController::class, 'update'])
    ->middleware('auth')->name('people.update');

Route::delete('people/{person}', [PersonController::class, 'destroy'])
    ->middleware('auth')->name('people.destroy');

Route::patch('people/{trashedPerson}/restore', [PersonController::class, 'restore'])
    ->middleware('auth')->name('people.restore');

Route::get('people/{anyPerson}/history', [PersonController::class, 'history'])
    ->middleware('auth')->name('people.history');

Route::put('people/{person}/visibility', [PersonController::class, 'changeVisibility'])
    ->middleware('auth')->name('people.changeVisibility');

Route::get('people/{person}/biography', [BiographyController::class, 'edit'])
    ->middleware('auth')->name('people.biography.edit');

Route::patch('people/{person}/biography', [BiographyController::class, 'update'])
    ->middleware('auth')->name('people.biography.update');
