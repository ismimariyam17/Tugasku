<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;


Route::get('/', [DatasetController::class, 'index'])->name('dashboard');
Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');
Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
Route::get('/datasets/{dataset}/download', [DatasetController::class, 'download'])->name('datasets.download');
// Tambahkan ini di bawah route show
Route::post('/datasets/{dataset}/predict', [DatasetController::class, 'predict'])->name('datasets.predict');
// Tambahkan di bawah route predict
Route::put('/datasets/{dataset}/clean', [DatasetController::class, 'clean'])->name('datasets.clean');
// Tambahkan di bawah route clean
Route::post('/datasets/{dataset}/cluster', [DatasetController::class, 'cluster'])->name('datasets.cluster');
Route::post('/datasets/{dataset}/classify', [DatasetController::class, 'classify'])->name('datasets.classify');