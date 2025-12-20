<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\CodeGeneratorController;
use App\Http\Controllers\DeepLearningController;

Route::get('/', function () {
    return view('main_dashboard');
});

Route::prefix('data-science')->group(function () {
    Route::get('/', [DatasetController::class, 'index'])->name('data_science.index');
    Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
    Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
    Route::get('/datasets/{dataset}/download', [DatasetController::class, 'download'])->name('datasets.download');
    Route::post('/datasets/{dataset}/predict', [DatasetController::class, 'predict'])->name('datasets.predict');
    Route::put('/datasets/{dataset}/clean', [DatasetController::class, 'clean'])->name('datasets.clean');
    Route::post('/datasets/{dataset}/cluster', [DatasetController::class, 'cluster'])->name('datasets.cluster');
    Route::post('/datasets/{dataset}/classify', [DatasetController::class, 'classify'])->name('datasets.classify');
});

Route::prefix('code-generator')->group(function () {
    Route::get('/', [CodeGeneratorController::class, 'index'])->name('code_generator.index');
    Route::post('/generate', [CodeGeneratorController::class, 'generate'])->name('code_generator.generate');
});

Route::get('/deep-learning', [DeepLearningController::class, 'index'])->name('deep_learning.index');
Route::post('/deep-learning/process', [DeepLearningController::class, 'process'])->name('deep_learning.process');

Route::get('/display-plot/{filename}', function ($filename) {
    $path = Storage::path('public/plots/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('display.plot');

Route::get('/download-model/{filename}', function ($filename) {
    $path = Storage::path('public/models/' . $filename);

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $path = str_replace('/', '\\', $path);
    }

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->download($path, 'trained_model.h5');
})->name('download.model');

// Tambahkan di bawah deep-learning/process
Route::post('/deep-learning/predict', [DeepLearningController::class, 'predict'])->name('deep_learning.predict');