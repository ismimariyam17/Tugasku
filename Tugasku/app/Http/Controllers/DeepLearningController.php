<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DeepLearningController extends Controller
{
    // Menampilkan halaman utama Deep Learning Studio
    public function index()
    {
        return view('deep_learning_index');
    }

    // Menangani proses upload dataset & training model
    public function process(Request $request)
    {
        // 1. VALIDASI INPUT USER
        // Memastikan file yang diupload aman dan parameter training valid.
        $request->validate([
            'dataset' => 'required|file|mimes:csv,json,xlsx,zip|max:20480', // Max 20MB
            'model_type' => 'required|string', // CNN, RNN, atau ANN
            'epochs' => 'required|integer|min:1|max:100', // Batasi epoch agar server tidak hang
            'learning_rate' => 'required|numeric',
        ]);

        try {
            // 2. PROSES FILE UPLOAD
            if ($request->hasFile('dataset')) {
                $file = $request->file('dataset');
                
                // Membuat nama file unik agar tidak tertimpa (timestamp + nama asli)
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Menyimpan file ke folder 'storage/app/datasets'
                // Pastikan folder ini ada permission tulisnya
                $filePath = $file->storeAs('datasets', $filename);
                
                // Mendapatkan Absolute Path (Lokasi lengkap di harddisk server)
                // Diperlukan agar script Python bisa menemukan filenya
                $absolutePath = storage_path('app/' . $filePath);

                // 3. MENJALANKAN SCRIPT PYTHON
                // Lokasi script python yang sudah kita buat sebelumnya
                $scriptPath = base_path('scripts/train_model.py');

                // Konfigurasi perintah Command Line
                // Perintah: python scripts/train_model.py --dataset "C:/..." --type "CNN" ...
                $process = new Process([
                    'python', // Ganti 'python3' jika di Mac/Linux atau jika 'python' tidak dikenali
                    $scriptPath,
                    '--dataset', $absolutePath,
                    '--type', $request->model_type,
                    '--epochs', $request->epochs,
                    '--lr', $request->learning_rate,
                    '--batch_size', $request->batch_size ?? 32 // Default 32 jika kosong
                ]);

                // Set Timeout (misal 5 menit / 300 detik) agar proses tidak diputus paksa saat training
                $process->setTimeout(300);
                
                // Eksekusi Perintah
                $process->run();

                // 4. CEK HASIL EKSEKUSI
                // Jika proses gagal (error di python), tangkap errornya
                if (!$process->isSuccessful()) {
                    // Mengambil pesan error dari Python (stderr)
                    throw new \Exception($process->getErrorOutput());
                }

                // 5. AMBIL OUTPUT DARI PYTHON
                // Output dari Python berupa string JSON (print(json.dumps(...)))
                $output = $process->getOutput();
                
                // Decode JSON menjadi Array PHP
                $result = json_decode($output, true);

                // Cek jika Python mengirimkan status error dalam JSON-nya
                if (isset($result['error'])) {
                    return back()->withErrors(['error' => 'Python Error: ' . $result['error']]);
                }

                // 6. SUKSES! TAMPILKAN HASIL
                // Format pesan sukses dengan data dari Python (Akurasi, Loss, dll)
                $pesanSukses = "Training Selesai! Model: {$result['model_type']} | " .
                               "Akurasi: {$result['accuracy']}% | " .
                               "Loss: {$result['loss']}";

                return back()->with('success', $pesanSukses);
            }

            return back()->withErrors(['dataset' => 'File dataset gagal diupload.']);

        } catch (\Exception $e) {
            // Menangkap semua error (baik dari PHP maupun Python)
            // Hapus file dataset jika terjadi error agar hemat storage (Opsional)
            // if (isset($filePath)) Storage::delete($filePath);

            return back()->withErrors(['error' => 'System Error: ' . $e->getMessage()]);
        }
    }
}