<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;

// 2. DASHBOARD UTAMA (LOBI MATA KULIAH)
// Ini rute baru '/dashboard'. Saat diakses, ia akan menampilkan file 'main_dashboard.blade.php' yang baru kita buat.
// Kita beri nama 'dashboard' agar konsisten dengan link di file lain.
Route::get('/', function () {
    return view('main_dashboard');
});
// 3. GRUP RUTE DATA SCIENCE
// Kita pindahkan semua rute DatasetController ke dalam prefix '/data-science'.
// Jadi URL-nya nanti menjadi: website.com/data-science, website.com/data-science/datasets/store, dst.
Route::prefix('data-science')->group(function () {
    
    // Halaman list dataset (tadinya ini halaman utama dashboard lama)
    // Namanya kita ubah jadi 'data_science.index' supaya tidak bentrok dengan dashboard utama.
    Route::get('/', [DatasetController::class, 'index'])->name('data_science.index');

    // Rute untuk upload dataset
    Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');

    // Rute untuk melihat detail/analisis dataset
    Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');

    // Rute hapus dataset
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');

    // Rute download dataset
    Route::get('/datasets/{dataset}/download', [DatasetController::class, 'download'])->name('datasets.download');

    // Rute prediksi regresi
    Route::post('/datasets/{dataset}/predict', [DatasetController::class, 'predict'])->name('datasets.predict');

    // Rute membersihkan data (missing values)
    Route::put('/datasets/{dataset}/clean', [DatasetController::class, 'clean'])->name('datasets.clean');

    // Rute clustering (K-Means)
    Route::post('/datasets/{dataset}/cluster', [DatasetController::class, 'cluster'])->name('datasets.cluster');

    // Rute klasifikasi (KNN)
    Route::post('/datasets/{dataset}/classify', [DatasetController::class, 'classify'])->name('datasets.classify');
});