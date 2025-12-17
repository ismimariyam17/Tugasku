<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $query = Dataset::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $datasets = $query->latest()->get();
        return view('dashboard', compact('datasets'));
    }

    public function store(Request $request)
    {
        $request->validate(['file_csv' => 'required|mimes:csv,txt']);

        $path = $request->file('file_csv')->store('datasets', 'public');
        $fullPath = Storage::disk('public')->path($path);
        
        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file); 
        
        if ($header === false) {
            fclose($file);
            return back()->withErrors(['file_csv' => 'File CSV kosong atau tidak valid.']);
        }

        $rowCount = 0;
        while (fgetcsv($file)) { $rowCount++; } 
        fclose($file);

        Dataset::create([
            'name' => $request->file('file_csv')->getClientOriginalName(),
            'file_path' => $path,
            'total_rows' => $rowCount,
            'total_columns' => count($header),
        ]);

        return back()->with('success', 'Dataset berhasil diupload!');
    }

    public function show(Dataset $dataset) 
    {
        $fullPath = Storage::disk('public')->path($dataset->file_path); 
        $file = fopen($fullPath, 'r'); 
        $header = fgetcsv($file);
        
        $rows = []; 
        $stats = [];
        $numericData = [];

        while (($row = fgetcsv($file)) !== false) { 
            $rows[] = $row; 
            foreach ($row as $index => $value) {
                if (is_numeric($value)) {
                    $numericData[$index][] = (float)$value;
                }
            }
        }
        fclose($file);

        foreach ($numericData as $index => $values) {
            $columnName = $header[$index] ?? 'Column ' . $index;
            $stats[$columnName] = [
                'mean' => count($values) > 0 ? array_sum($values) / count($values) : 0,
                'max' => count($values) > 0 ? max($values) : 0,
                'min' => count($values) > 0 ? min($values) : 0,
                'count' => count($values)
            ];
        }

        $previewRows = array_slice($rows, 0, 50);

        return view('show_dataset', compact('dataset', 'header', 'previewRows', 'stats')); 
    }

    public function download(Dataset $dataset)
    {
        return Storage::disk('public')->download($dataset->file_path, $dataset->name);
    }

    public function destroy(Dataset $dataset) 
    {
        Storage::disk('public')->delete($dataset->file_path); 
        $dataset->delete(); 
        return back()->with('success', 'Dataset berhasil dihapus!');  
    }
}