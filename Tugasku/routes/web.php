<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;


Route::get('/', [DatasetController::class, 'index'])->name('dashboard');
Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
