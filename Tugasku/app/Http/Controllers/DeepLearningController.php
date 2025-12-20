<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use App\Models\TrainingHistory; 

class DeepLearningController extends Controller
{
    public function index()
    {
        $histories = TrainingHistory::latest()->get();
        return view('deep_learning_index', compact('histories'));
    }

    private function runPythonScript($mode, $request, $datasetPath)
    {
        $modelPath = Storage::path('public/models/model_latest.h5');
        $scalerPath = Storage::path('public/models/scaler_latest.save');
        
        Storage::makeDirectory('public/models');
        Storage::makeDirectory('public/plots');

        $plotPath = "";
        $plotFilename = ""; 

        if ($mode == 'train') {
            $plotFilename = 'plot_' . time() . '.png';
            $plotPath = Storage::path('public/plots/' . $plotFilename);
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $datasetPath = str_replace('/', '\\', $datasetPath);
            $modelPath = str_replace('/', '\\', $modelPath);
            $scalerPath = str_replace('/', '\\', $scalerPath);
            $plotPath = str_replace('/', '\\', $plotPath);
        }

        $scriptPath = base_path('scripts/train_model.py');
        $pythonPath = "C:\\laragon\\bin\\python\\python-3.10\\python.EXE"; 

        $args = [
            $pythonPath, $scriptPath,
            '--mode', $mode,
            '--dataset', $datasetPath,
            '--model_file', $modelPath,
            '--scaler_file', $scalerPath
        ];

        if ($mode == 'train') {
            $args = array_merge($args, [
                '--type', $request->model_type,
                '--epochs', (string)$request->epochs,
                '--lr', (string)$request->learning_rate,
                '--plot_file', $plotPath
            ]);
        }

        $process = new Process($args);
        
        $process->setEnv([
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows', 
            'TEMP' => getenv('TEMP') ?: 'C:\\Windows\\Temp',
            'TMP' => getenv('TMP') ?: 'C:\\Windows\\Temp',
            'PATH' => getenv('PATH'),
            'MPLCONFIGDIR' => getenv('TEMP') ?: 'C:\\Windows\\Temp',
            'USERPROFILE' => getenv('USERPROFILE') ?: 'C:\\Users\\Public'
        ]);
        
        $process->setTimeout(600);
        $process->run();

        $output = $process->getOutput();
        $result = json_decode($output, true);

        if (!$process->isSuccessful() && !isset($result['error'])) {
             dd(['STATUS' => 'PYTHON ERROR', 'MSG' => $process->getErrorOutput(), 'OUT' => $output]);
        }

        if (isset($result['error'])) return ['error' => $result['error']];

        if ($mode == 'train') {
            $result['plot_url'] = route('display.plot', ['filename' => $plotFilename]);
            $result['model_url'] = route('download.model', ['filename' => 'model_latest.h5']);
            
            TrainingHistory::create([
                'model_type' => $request->model_type,
                'epochs' => $request->epochs,
                'accuracy' => $result['accuracy'],
                'loss' => $result['loss'],
                'plot_file' => $plotFilename,
                'model_file' => 'model_latest.h5'
            ]);
        }

        return $result;
    }

    public function process(Request $request)
    {
        $request->validate([
            'dataset' => 'required|file|mimes:csv,xlsx',
            'model_type' => 'required|string', 
            'epochs' => 'required|integer',
            'learning_rate' => 'required|numeric',
        ]);

        $file = $request->file('dataset');
        $path = $file->storeAs('datasets', 'train_' . time() . '.csv');
        $absPath = Storage::path($path);

        $result = $this->runPythonScript('train', $request, $absPath);

        if (isset($result['error'])) return back()->withErrors(['error' => $result['error']]);

        return back()
            ->with('success', 'Training Selesai! Data disimpan ke Riwayat.')
            ->with('training_result', $result);
    }

    public function predict(Request $request)
    {
        $request->validate(['dataset_predict' => 'required|file|mimes:csv,xlsx']);

        $file = $request->file('dataset_predict');
        $path = $file->storeAs('datasets', 'predict_' . time() . '.csv');
        $absPath = Storage::path($path);

        $result = $this->runPythonScript('predict', $request, $absPath);

        if (isset($result['error'])) return back()->withErrors(['error' => $result['error']]);

        // --- UPDATE: Kirim data lengkap (Kelas + Confidence) ke View ---
        return back()->with('prediction_data', $result['predictions']);
    }
}