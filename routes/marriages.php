<?php

use App\Http\Controllers\MarriageController;
use Illuminate\Support\Facades\Route;

Route::get('marriages/create', [MarriageController::class, 'create'])
    ->middleware('auth')->name('marriages.create');

Route::post('marriages', [MarriageController::class, 'store'])
    ->middleware('auth')->name('marriages.store');

Route::get('marriages/{marriage}/edit', [MarriageController::class, 'edit'])
    ->middleware('auth')->name('marriages.edit');

Route::match(['put', 'patch'], 'marriages/{marriage}', [MarriageController::class, 'update'])
    ->middleware('auth')->name('marriages.update');

Route::delete('marriages/{marriage}', [MarriageController::class, 'destroy'])
    ->middleware('auth')->name('marriages.destroy');

Route::patch('marriages/{trashedMarriage}/restore', [MarriageController::class, 'restore'])
    ->middleware('auth')->name('marriages.restore');

Route::get('marriages/{anyMarriage}/history', [MarriageController::class, 'history'])
    ->middleware('auth')->name('marriages.history');
