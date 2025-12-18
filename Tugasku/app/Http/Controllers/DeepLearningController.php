<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DeepLearningController extends Controller
{
    public function index()
    {
        return view('deep_learning_index');
    }

    public function process(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'dataset' => 'required|file|mimes:csv,json,xlsx,zip|max:51200', 
            'model_type' => 'required|string', 
            'epochs' => 'required|integer|min:1|max:100',
            'learning_rate' => 'required|numeric',
        ]);

        try {
            if ($request->hasFile('dataset')) {
                // A. SIMPAN DATASET
                $file = $request->file('dataset');
                $filename = time() . '_' . $file->getClientOriginalName();
                $relativePath = $file->storeAs('datasets', $filename);
                $absolutePath = Storage::path($relativePath);
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $absolutePath = str_replace('/', '\\', $absolutePath);

                // B. PERSIAPAN FILE PLOT
                $plotFilename = 'plot_' . time() . '.png';
                Storage::makeDirectory('public/plots');
                $plotAbsolutePath = Storage::path('public/plots/' . $plotFilename);
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $plotAbsolutePath = str_replace('/', '\\', $plotAbsolutePath);

                // C. PERSIAPAN FILE MODEL (FITUR BARU)
                $modelFilename = 'model_' . time() . '.h5';
                Storage::makeDirectory('public/models'); // Buat folder models
                $modelAbsolutePath = Storage::path('public/models/' . $modelFilename);
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $modelAbsolutePath = str_replace('/', '\\', $modelAbsolutePath);

                // D. JALANKAN PYTHON
                $scriptPath = base_path('scripts/train_model.py');
                $pythonPath = "C:\\laragon\\bin\\python\\python-3.10\\python.EXE"; 

                $process = new Process([
                    $pythonPath, 
                    $scriptPath,
                    '--dataset', $absolutePath,
                    '--type', $request->model_type,
                    '--epochs', (string)$request->epochs,
                    '--lr', (string)$request->learning_rate,
                    '--batch_size', '32',
                    '--plot_file', $plotAbsolutePath,
                    '--model_file', $modelAbsolutePath // <-- Kirim Path Model
                ]);

                // Environment Variables
                $process->setEnv([
                    'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows', 
                    'TEMP' => getenv('TEMP') ?: 'C:\\Windows\\Temp',
                    'TMP' => getenv('TMP') ?: 'C:\\Windows\\Temp',
                    'PATH' => getenv('PATH'),
                    'MPLCONFIGDIR' => getenv('TEMP') ?: 'C:\\Windows\\Temp',
                    'USERPROFILE' => getenv('USERPROFILE') ?: 'C:\\Users\\Public',
                    'HOMEDRIVE' => getenv('HOMEDRIVE') ?: 'C:',
                    'HOMEPATH' => getenv('HOMEPATH') ?: '\\Users\\Public',
                ]);
                
                $process->setTimeout(600);
                $process->run();

                // E. CEK HASIL
                $output = $process->getOutput();
                $result = json_decode($output, true);

                if (!$process->isSuccessful() && !isset($result['error']) && !isset($result['status'])) {
                     dd(['STATUS' => 'PYTHON ERROR', 'MSG' => $process->getErrorOutput(), 'OUT' => $output]);
                }

                if (isset($result['error'])) {
                    return back()->withErrors(['error' => 'Error: ' . $result['error']]);
                }

                // F. SUKSES
                if (isset($result['status']) && $result['status'] == 'success') {
                    
                    // URL Grafik & URL Download Model
                    $plotUrl = route('display.plot', ['filename' => $plotFilename]);
                    $modelUrl = route('download.model', ['filename' => $modelFilename]);

                    $pesanSukses = "Training Selesai! Model disimpan.";

                    return back()
                        ->with('success', $pesanSukses)
                        ->with('plot_url', $plotUrl) 
                        ->with('model_url', $modelUrl) // <-- Kirim Link Download
                        ->with('training_result', $result);
                }
            }
            return back()->withErrors(['dataset' => 'Gagal upload file.']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'System Error: ' . $e->getMessage()]);
        }
    }
    public function displayPlot(Request $request)
    { 
        $filename = $request->query('filename');
        $filePath = storage_path('app/public/plots/' . $filename);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath);
    }
    public function downloadModel(Request $request)
    {
        $filename = $request->query('filename');
        $filePath = storage_path('app/public/models/' . $filename);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}

    