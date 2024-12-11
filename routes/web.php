<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::get('/', [PetController::class, 'index'])->name('pets.index');
Route::get('/about', [PetController::class, 'about'])->name('about');

Route::resource('pets', PetController::class)->parameters([
    'pets' => 'id',
]);
//
//Route::get('/pets/ajax', [PetController::class, 'ajaxIndex'])->name('pets.ajax');

