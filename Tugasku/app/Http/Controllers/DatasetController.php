<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $datasets = Dataset::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->latest()
            ->get();
            
        return view('data_science_index', compact('datasets'));
    }

    public function store(Request $request)
    {
        $request->validate(['file_csv' => 'required|mimes:csv,txt|max:10240']);
        
        $path = $request->file('file_csv')->store('datasets', 'public');
        $originalName = $request->file('file_csv')->getClientOriginalName();

        $dataset = Dataset::create([
            'name' => $originalName,
            'file_path' => $path,
            'total_rows' => 0, 
            'total_columns' => 0,
            'status' => 'pending'
        ]);

        $this->processAnalysis($dataset);

        return back()->with('success', 'Dataset berhasil diupload & dianalisis!');
    }

public function show(Dataset $dataset) 
    {
        // ... (Kode load analisis/stats yang lama tetap sama) ...
        
        // Cek jika analisis belum ada, jalankan proses
        if (empty($dataset->analysis)) {
            $this->processAnalysis($dataset);
            $dataset->refresh();
        }
        $stats = $dataset->analysis;
        
        // --- TAMBAHAN BARU: CORRELATION MATRIX ---
        // 1. Ambil path file fisik untuk dibaca datanya
        $fullPath = Storage::disk('public')->path($dataset->file_path);
        
        // 2. Baca file CSV lagi untuk mengambil semua data angka
        $numericData = [];
        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file);
        
        // 3. Kita perlu tahu index kolom mana saja yang numeric
        $numericIndices = [];
        foreach ($stats as $col => $data) {
            // Cek apakah kolom ini tipe-nya 'numeric'
            if ($data['type'] == 'numeric') {
                // Cari posisi index (urutan) kolom tersebut di header
                $idx = array_search($col, $header);
                if ($idx !== false) $numericIndices[$col] = $idx;
            }
        }

        // 4. Loop baris demi baris untuk mengumpulkan data angka saja
        while (($row = fgetcsv($file)) !== false) {
            foreach ($numericIndices as $colName => $idx) {
                // Simpan data jika ada isinya dan berupa angka
                if (isset($row[$idx]) && is_numeric($row[$idx])) {
                    $numericData[$colName][] = (float)$row[$idx];
                }
            }
        }
        fclose($file);

        // 5. Hitung Korelasi Antar Semua Pasangan Kolom
        $correlationMatrix = [];
        $columns = array_keys($numericData); // Daftar nama kolom angka

        // Loop Kolom A
        foreach ($columns as $colA) {
            // Loop Kolom B
            foreach ($columns as $colB) {
                if ($colA == $colB) {
                    // Korelasi dengan diri sendiri pasti 1 (100%)
                    $correlationMatrix[$colA][$colB] = 1; 
                } else {
                    // Hitung korelasi A vs B pakai rumus Pearson
                    $correlationMatrix[$colA][$colB] = $this->calculateCorrelationSimple(
                        $numericData[$colA], 
                        $numericData[$colB]
                    );
                }
            }
        }
        
        // Ambil preview row seperti biasa
        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file);
        $previewRows = [];
        for ($i = 0; $i < 50; $i++) {
            if (($r = fgetcsv($file)) !== false) $previewRows[] = $r;
        }
        fclose($file);

        // Kirim variable $correlationMatrix ke View
        return view('show_dataset', compact('dataset', 'header', 'previewRows', 'stats', 'correlationMatrix')); 
    }

    // --- HELPER BARU: Rumus Pearson Correlation ---
    private function calculateCorrelationSimple($array1, $array2) {
        $n = count($array1);
        // Jika jumlah data beda atau kosong, tidak bisa dihitung
        if ($n !== count($array2) || $n == 0) return 0;

        $sum1 = array_sum($array1);
        $sum2 = array_sum($array2);
        
        $sum1Sq = 0; 
        $sum2Sq = 0; 
        $pSum = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sum1Sq += pow($array1[$i], 2); // Sigma X Kuadrat
            $sum2Sq += pow($array2[$i], 2); // Sigma Y Kuadrat
            $pSum += ($array1[$i] * $array2[$i]); // Sigma X*Y
        }
        
        // Rumus Bagian Atas (Numerator)
        $num = $pSum - (($sum1 * $sum2) / $n);
        
        // Rumus Bagian Bawah (Denominator)
        $den = sqrt(($sum1Sq - pow($sum1, 2) / $n) * ($sum2Sq - pow($sum2, 2) / $n));
        
        // Hindari pembagian dengan nol
        return $den == 0 ? 0 : $num / $den;
    }
    private function processAnalysis(Dataset $dataset)
    {
        $fullPath = Storage::disk('public')->path($dataset->file_path);
        
        if (!file_exists($fullPath)) return;

        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file);

        if (!$header) {
            fclose($file);
            return;
        }

        $columnsData = [];
        $rowCount = 0;
        
        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;
            foreach ($row as $index => $value) {
                $columnsData[$index][] = ($value === '' ? null : $value);
            }
        }
        fclose($file);

        $analysisResult = [];
        
        foreach ($columnsData as $index => $values) {
            $colName = $header[$index] ?? "Col_$index";
            
            $nonNullValues = array_filter($values, fn($v) => !is_null($v));
            $isNumeric = !empty($nonNullValues) && count(array_filter($nonNullValues, 'is_numeric')) === count($nonNullValues);

            $totalData = count($values);
            $missingCount = $totalData - count($nonNullValues);
            
            $stats = [
                'type' => $isNumeric ? 'numeric' : 'categorical',
                'missing' => $missingCount,
                'count' => $totalData
            ];

            if ($isNumeric && !empty($nonNullValues)) {
                $numericValues = array_map('floatval', $nonNullValues);
                sort($numericValues);
                $n = count($numericValues);

                $stats['min'] = $numericValues[0];
                $stats['max'] = $numericValues[$n - 1];
                $stats['mean'] = array_sum($numericValues) / $n;
                
                $variance = 0;
                foreach ($numericValues as $v) {
                    $variance += pow($v - $stats['mean'], 2);
                }
                $stats['std_dev'] = $n > 1 ? sqrt($variance / ($n - 1)) : 0;

                $stats['q1'] = $this->calculateQuartile($numericValues, 0.25);
                $stats['median'] = $this->calculateQuartile($numericValues, 0.50);
                $stats['q3'] = $this->calculateQuartile($numericValues, 0.75);
                
                $iqr = $stats['q3'] - $stats['q1'];
                $stats['iqr'] = $iqr;
                $stats['lower_bound'] = $stats['q1'] - (1.5 * $iqr);
                $stats['upper_bound'] = $stats['q3'] + (1.5 * $iqr);

            } else {
                $counts = array_count_values(array_map('strval', $nonNullValues));
                arsort($counts);
                $topCategories = array_slice($counts, 0, 5, true);
                
                $stats['unique_count'] = count($counts);
                $stats['top_categories'] = $topCategories;
            }

            $analysisResult[$colName] = $stats;
        }

        $dataset->update([
            'total_rows' => $rowCount,
            'total_columns' => count($header),
            'analysis_json' => json_encode($analysisResult),
            'status' => 'completed'
        ]);
    }

    private function calculateQuartile($sortedArray, $percentile) {
        $count = count($sortedArray);
        $index = ($count - 1) * $percentile;
        $floor = floor($index);
        $ceil = ceil($index);
        
        if ($floor == $ceil) {
            return $sortedArray[$index];
        }
        
        $d0 = $sortedArray[$floor] * ($ceil - $index);
        $d1 = $sortedArray[$ceil] * ($index - $floor);
        return $d0 + $d1;
    }

    public function download(Dataset $dataset)
    {
        return Storage::disk('public')->download($dataset->file_path, $dataset->name);
    }

    public function destroy(Dataset $dataset) 
    {
        if (Storage::disk('public')->exists($dataset->file_path)) {
            Storage::disk('public')->delete($dataset->file_path);
        }
        $dataset->delete(); 
        return back()->with('success', 'Dataset berhasil dihapus!');  
    }

    // ... function lainnya ...

    public function predict(Request $request, Dataset $dataset)
    {
        $request->validate([
            'col_x' => 'required', // Variable Independen (Penyebab)
            'col_y' => 'required', // Variable Dependen (Akibat)
        ]);

        $colX = $request->col_x;
        $colY = $request->col_y;

        // 1. Ambil Data Fisik
        $fullPath = Storage::disk('public')->path($dataset->file_path);
        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file);
        
        // Cari index kolom X dan Y
        $indexX = array_search($colX, $header);
        $indexY = array_search($colY, $header);

        $dataPoints = [];
        $xValues = [];
        $yValues = [];

        while (($row = fgetcsv($file)) !== false) {
            // Hanya ambil jika kedua data ada dan numeric
            if (isset($row[$indexX]) && isset($row[$indexY]) && 
                is_numeric($row[$indexX]) && is_numeric($row[$indexY])) {
                
                $valX = (float)$row[$indexX];
                $valY = (float)$row[$indexY];
                
                $xValues[] = $valX;
                $yValues[] = $valY;
                $dataPoints[] = ['x' => $valX, 'y' => $valY];
            }
        }
        fclose($file);

        if (count($dataPoints) < 2) {
            return back()->with('error', 'Data tidak cukup untuk melakukan prediksi.');
        }

        // 2. Rumus Linear Regression (Least Squares Method)
        // y = mx + c
        // m (slope) = (n * sum(xy) - sum(x) * sum(y)) / (n * sum(x^2) - (sum(x))^2)
        // c (intercept) = (sum(y) - m * sum(x)) / n

        $n = count($dataPoints);
        $sumX = array_sum($xValues);
        $sumY = array_sum($yValues);
        $sumXY = 0;
        $sumXX = 0;

        foreach ($dataPoints as $point) {
            $sumXY += ($point['x'] * $point['y']);
            $sumXX += ($point['x'] * $point['x']);
        }

        $denominator = ($n * $sumXX) - ($sumX * $sumX);
        
        if ($denominator == 0) {
            return back()->with('error', 'Tidak bisa membuat garis regresi (Varians X nol).');
        }

        $m = (($n * $sumXY) - ($sumX * $sumY)) / $denominator; // Kemiringan garis
        $c = ($sumY - ($m * $sumX)) / $n; // Titik potong sumbu Y

        // 3. Buat Garis Prediksi untuk Visualisasi
        $minX = min($xValues);
        $maxX = max($xValues);
        
        $predictionLine = [
            ['x' => $minX, 'y' => ($m * $minX) + $c],
            ['x' => $maxX, 'y' => ($m * $maxX) + $c]
        ];

        // 4. Hitung Korelasi (Pearson Correlation) - Seberapa kuat hubungannya?
        // r = (n(∑xy) - (∑x)(∑y)) / sqrt([n∑x^2 - (∑x)^2][n∑y^2 - (∑y)^2])
        $sumYY = 0;
        foreach ($yValues as $val) $sumYY += ($val * $val);
        
        $bawah = sqrt((($n * $sumXX) - ($sumX * $sumX)) * (($n * $sumYY) - ($sumY * $sumY)));
        $correlation = $bawah != 0 ? (($n * $sumXY) - ($sumX * $sumY)) / $bawah : 0;

        // Kirim hasil balik ke view dengan session flash
        return back()->with('prediction_result', [
            'm' => $m,
            'c' => $c,
            'correlation' => $correlation,
            'col_x' => $colX,
            'col_y' => $colY,
            'points' => $dataPoints,
            'line' => $predictionLine
        ]);
    }
    public function clean(Dataset $dataset)
    {
        // 1. Ambil Statistik yang sudah ada (untuk tahu Mean & Modus)
        $stats = $dataset->analysis;
        if (!$stats) {
            return back()->withErrors(['error' => 'Analisis data belum tersedia.']);
        }

        $fullPath = Storage::disk('public')->path($dataset->file_path);
        
        // Buat file temporary untuk menyimpan hasil cleaning
        $tempPath = $fullPath . '.tmp'; 
        $inputFile = fopen($fullPath, 'r');
        $outputFile = fopen($tempPath, 'w');
        
        // Ambil Header
        $header = fgetcsv($inputFile);
        fputcsv($outputFile, $header); // Tulis ulang header ke file baru

        // Siapkan "Replacement Values" (Nilai Pengganti)
        $replacements = [];
        foreach ($header as $index => $colName) {
            // Gunakan nama kolom dari header, atau fallback ke Col_X
            $key = $colName ?? "Col_$index"; 
            
            // Cek statistik kolom tersebut di JSON
            if (isset($stats[$key])) {
                if ($stats[$key]['type'] == 'numeric') {
                    // Jika angka, siapkan MEAN sebagai penambal
                    $replacements[$index] = $stats[$key]['mean'];
                } else {
                    // Jika teks, siapkan TOP CATEGORY (Modus) sebagai penambal
                    // Ambil key pertama dari array top_categories
                    $topCat = array_key_first($stats[$key]['top_categories'] ?? []);
                    $replacements[$index] = $topCat ?? 'Unknown';
                }
            }
        }

        // 2. Proses Cleaning Baris demi Baris
        $fixedCount = 0;
        while (($row = fgetcsv($inputFile)) !== false) {
            $newRow = [];
            foreach ($row as $index => $cell) {
                // Jika sel kosong (null atau string kosong)
                if ($cell === '' || $cell === null) {
                    // Isi dengan nilai pengganti yang sudah disiapkan
                    $newRow[] = $replacements[$index] ?? $cell;
                    $fixedCount++;
                } else {
                    $newRow[] = $cell;
                }
            }
            fputcsv($outputFile, $newRow);
        }

        fclose($inputFile);
        fclose($outputFile);

        // 3. Timpa File Lama dengan File Baru yang Bersih
        rename($tempPath, $fullPath);

        // 4. Analisis Ulang (Re-Analyze) agar statistik di DB update
        $this->processAnalysis($dataset);

        return back()->with('success', "Data berhasil dibersihkan! $fixedCount sel kosong telah diisi otomatis.");
    }
    // --- FITUR BARU: K-MEANS CLUSTERING ---
    public function cluster(Request $request, Dataset $dataset)
    {
        $request->validate([
            'col_x' => 'required',
            'col_y' => 'required',
            'k' => 'required|integer|min:2|max:5' // Batasi maks 5 cluster biar tidak pusing liat warnanya
        ]);

        $colX = $request->col_x;
        $colY = $request->col_y;
        $k = $request->k;

        // 1. Ambil Data
        $fullPath = Storage::disk('public')->path($dataset->file_path);
        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file);
        
        $indexX = array_search($colX, $header);
        $indexY = array_search($colY, $header);

        $rawData = [];
        while (($row = fgetcsv($file)) !== false) {
            if (isset($row[$indexX]) && isset($row[$indexY]) && 
                is_numeric($row[$indexX]) && is_numeric($row[$indexY])) {
                $rawData[] = [
                    'x' => (float)$row[$indexX], 
                    'y' => (float)$row[$indexY]
                ];
            }
        }
        fclose($file);

        if (count($rawData) < $k) {
            return back()->with('error', 'Jumlah data terlalu sedikit untuk clustering.');
        }

        // 2. Normalisasi Data (Min-Max Scaling)
        // K-Means sensitif terhadap skala. Gaji (Jutaan) vs Umur (Puluhan) akan kacau jika tidak dinormalisasi.
        $xValues = array_column($rawData, 'x');
        $yValues = array_column($rawData, 'y');
        
        $minX = min($xValues); $maxX = max($xValues);
        $minY = min($yValues); $maxY = max($yValues);
        
        $normalizedData = [];
        foreach ($rawData as $i => $point) {
            $normalizedData[$i] = [
                'norm_x' => ($point['x'] - $minX) / ($maxX - $minX ?: 1),
                'norm_y' => ($point['y'] - $minY) / ($maxY - $minY ?: 1),
                'orig_x' => $point['x'],
                'orig_y' => $point['y'],
                'cluster' => 0
            ];
        }

        // 3. Inisialisasi Centroid Awal (Random Pick)
        $centroids = [];
        $randomKeys = array_rand($normalizedData, $k);
        if (!is_array($randomKeys)) $randomKeys = [$randomKeys];
        
        foreach ($randomKeys as $key) {
            $centroids[] = $normalizedData[$key]; // Ambil titik acak sebagai pusat awal
        }

        // 4. Iterasi K-Means
        $maxIterations = 20; // Cukup 20 kali perulangan
        for ($iter = 0; $iter < $maxIterations; $iter++) {
            $clusters = array_fill(0, $k, []);
            $changes = 0;

            // A. Assign setiap titik ke Centroid terdekat
            foreach ($normalizedData as $key => $point) {
                $minDist = null;
                $bestCluster = 0;

                foreach ($centroids as $cIndex => $centroid) {
                    // Euclidean Distance
                    $dist = sqrt(pow($point['norm_x'] - $centroid['norm_x'], 2) + pow($point['norm_y'] - $centroid['norm_y'], 2));
                    
                    if ($minDist === null || $dist < $minDist) {
                        $minDist = $dist;
                        $bestCluster = $cIndex;
                    }
                }

                if ($normalizedData[$key]['cluster'] !== $bestCluster) {
                    $normalizedData[$key]['cluster'] = $bestCluster;
                    $changes++;
                }
                
                $clusters[$bestCluster][] = $point;
            }

            // Jika tidak ada data yang pindah cluster, berarti sudah konvergen (selesai)
            if ($changes === 0) break;

            // B. Hitung ulang posisi Centroid (Rata-rata posisi anggota cluster)
            foreach ($clusters as $cIndex => $members) {
                if (count($members) > 0) {
                    $avgX = array_sum(array_column($members, 'norm_x')) / count($members);
                    $avgY = array_sum(array_column($members, 'norm_y')) / count($members);
                    $centroids[$cIndex]['norm_x'] = $avgX;
                    $centroids[$cIndex]['norm_y'] = $avgY;
                }
            }
        }

        // 5. Siapkan Output untuk Chart
        $chartData = [];
        foreach ($normalizedData as $p) {
            $chartData[$p['cluster']][] = ['x' => $p['orig_x'], 'y' => $p['orig_y']];
        }

        return back()->with('cluster_result', [
            'k' => $k,
            'col_x' => $colX,
            'col_y' => $colY,
            'clusters' => $chartData,
            'iterations' => $iter
        ]);
    }
    // --- FITUR BARU: KNN CLASSIFICATION ---
    public function classify(Request $request, Dataset $dataset)
    {
        $request->validate([
            'target_col' => 'required', // Kolom Label (misal: Ukuran Baju, Lulus/Gagal)
            'features' => 'required|array', // Kolom Fitur (misal: Tinggi, Berat)
            'input_values' => 'required|array', // Nilai input dari user
            'k' => 'required|integer|min:1|max:21' // Jumlah tetangga
        ]);

        $targetCol = $request->target_col;
        $featureCols = $request->features;
        $inputValues = array_map('floatval', $request->input_values); // Pastikan input angka
        $k = (int) $request->k;

        // 1. Baca Data
        $fullPath = Storage::disk('public')->path($dataset->file_path);
        $file = fopen($fullPath, 'r');
        $header = fgetcsv($file);
        
        // Mapping Index Kolom
        $targetIndex = array_search($targetCol, $header);
        $featureIndices = [];
        foreach ($featureCols as $f) {
            $featureIndices[] = array_search($f, $header);
        }

        $distances = [];

        // 2. Hitung Jarak (Euclidean Distance) setiap baris data ke Input User
        while (($row = fgetcsv($file)) !== false) {
            // Skip jika data target kosong
            if (!isset($row[$targetIndex]) || $row[$targetIndex] === '') continue;

            $sumSquaredDiff = 0;
            $isValidRow = true;

            foreach ($featureIndices as $idx => $fIndex) {
                // Pastikan fitur ada isinya dan angka
                if (!isset($row[$fIndex]) || !is_numeric($row[$fIndex])) {
                    $isValidRow = false; 
                    break;
                }
                
                // Rumus: (Data - Input)^2
                $diff = (float)$row[$fIndex] - $inputValues[$idx];
                $sumSquaredDiff += ($diff * $diff);
            }

            if ($isValidRow) {
                $distances[] = [
                    'label' => $row[$targetIndex], // Simpan labelnya (misal: "Lulus")
                    'distance' => sqrt($sumSquaredDiff) // Akar kuadrat jarak
                ];
            }
        }
        fclose($file);

        if (count($distances) < $k) {
            return back()->with('error', 'Data tidak cukup untuk melakukan KNN.');
        }

        // 3. Urutkan dari jarak terdekat (ASC)
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // 4. Ambil K Tetangga Terdekat
        $nearestNeighbors = array_slice($distances, 0, $k);

        // 5. Voting (Cari label yang paling banyak muncul)
        $votes = [];
        foreach ($nearestNeighbors as $neighbor) {
            $label = $neighbor['label'];
            if (!isset($votes[$label])) $votes[$label] = 0;
            $votes[$label]++;
        }

        // Urutkan voting terbanyak
        arsort($votes);
        $prediction = array_key_first($votes);

        return back()->with('classification_result', [
            'prediction' => $prediction,
            'input_values' => $inputValues,
            'feature_cols' => $featureCols,
            'target_col' => $targetCol,
            'neighbors' => $nearestNeighbors, // Untuk ditampilkan di tabel bukti
            'votes' => $votes
        ]);
    }
}