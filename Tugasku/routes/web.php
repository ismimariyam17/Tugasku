<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;


Route::get('/', [DatasetController::class, 'index'])->name('dashboard');
Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');
Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
Route::get('/datasets/{dataset}/download', [DatasetController::class, 'download'])->name('datasets.download');
