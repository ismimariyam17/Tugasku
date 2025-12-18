<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis: {{ $dataset->name }} - Tugasku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: { bg: '#0B1120', surface: '#151F32', border: '#1E293B', input: '#0F172A' },
                        accent: { cyan: '#06b6d4', purple: '#8b5cf6', emerald: '#10b981', rose: '#f43f5e' }
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0B1120; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .glass { background: rgba(21, 31, 50, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased min-h-screen pb-20">

    <header class="sticky top-0 z-50 glass border-b border-dark-border h-16 flex items-center justify-between px-6 shadow-lg shadow-black/20">
        <div class="flex items-center gap-4">
            <a href="{{ route('data_science.index') }}" class="p-2 rounded-lg hover:bg-dark-input text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="font-bold text-white text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ $dataset->name }}
                </h1>
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <span>{{ number_format($dataset->total_rows) }} Baris</span>
                    <span>•</span>
                    <span>{{ $dataset->total_columns }} Kolom</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
             <form action="{{ route('datasets.clean', $dataset->id) }}" method="POST">
                @csrf @method('PUT')
                <button type="submit" class="px-4 py-2 bg-dark-input hover:bg-accent-emerald hover:text-white border border-dark-border rounded-lg text-xs font-bold text-emerald-500 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Auto Clean
                </button>
            </form>

            <a href="{{ route('datasets.download', $dataset->id) }}" class="p-2 text-slate-400 hover:text-accent-cyan transition-colors" title="Download CSV">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </a>
            
            <form action="{{ route('datasets.destroy', $dataset->id) }}" method="POST" onsubmit="return confirm('Hapus dataset ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 text-slate-400 hover:text-accent-rose transition-colors" title="Hapus Dataset">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </header>

    <main class="p-6 max-w-7xl mx-auto space-y-8">
        
        @if(session('success'))
        <div class="bg-accent-emerald/10 border border-accent-emerald/20 text-accent-emerald px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-accent-rose/10 border border-accent-rose/20 text-accent-rose px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
        @endif

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-dark-surface border border-dark-border rounded-2xl overflow-hidden flex flex-col h-[500px]">
                <div class="p-4 border-b border-dark-border bg-dark-input/30">
                    <h3 class="font-bold text-white">Variabel & Statistik</h3>
                </div>
                <div class="overflow-y-auto flex-1 p-2 space-y-2 custom-scrollbar">
                    @foreach($stats as $col => $data)
                    <div x-data="{ open: false }" class="bg-dark-bg border border-dark-border rounded-lg p-3 hover:border-accent-cyan/30 transition-colors">
                        <div @click="open = !open" class="flex justify-between items-center cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $data['type'] == 'numeric' ? 'bg-accent-cyan' : 'bg-accent-purple' }}"></span>
                                <span class="font-mono text-sm font-semibold text-slate-200 truncate w-32" title="{{ $col }}">{{ $col }}</span>
                            </div>
                            <span class="text-[10px] uppercase tracking-wider text-slate-500">{{ $data['type'] }}</span>
                        </div>
                        
                        <div x-show="open" class="mt-3 pt-3 border-t border-dark-border text-xs text-slate-400 space-y-1">
                            <div class="flex justify-between"><span>Missing:</span> <span class="{{ $data['missing'] > 0 ? 'text-accent-rose' : 'text-emerald-500' }}">{{ $data['missing'] }}</span></div>
                            @if($data['type'] == 'numeric')
                                <div class="flex justify-between"><span>Min:</span> <span class="text-white">{{ number_format($data['min'], 2) }}</span></div>
                                <div class="flex justify-between"><span>Max:</span> <span class="text-white">{{ number_format($data['max'], 2) }}</span></div>
                                <div class="flex justify-between"><span>Mean:</span> <span class="text-accent-cyan">{{ number_format($data['mean'], 2) }}</span></div>
                            @else
                                <div class="flex justify-between"><span>Unique:</span> <span class="text-white">{{ $data['unique_count'] }}</span></div>
                                <div class="mt-1">
                                    <span class="block mb-1 text-slate-500">Top Categories:</span>
                                    @foreach($data['top_categories'] as $cat => $count)
                                        <div class="flex justify-between pl-2"><span>{{ Str::limit($cat, 15) }}</span> <span>{{ $count }}</span></div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 bg-dark-surface border border-dark-border rounded-2xl overflow-hidden flex flex-col h-[500px]">
                <div class="p-4 border-b border-dark-border bg-dark-input/30 flex justify-between items-center">
                    <h3 class="font-bold text-white">Preview Data (50 Baris Pertama)</h3>
                    <span class="text-xs text-slate-500 font-mono">Read-Only</span>
                </div>
                <div class="overflow-auto flex-1 custom-scrollbar">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-dark-input text-xs uppercase text-slate-400 sticky top-0 z-10">
                            <tr>
                                @foreach($header as $h)
                                <th class="px-4 py-3 font-medium border-b border-dark-border">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-border text-slate-300">
                            @foreach($previewRows as $row)
                            <tr class="hover:bg-white/5 transition-colors">
                                @foreach($row as $cell)
                                <td class="px-4 py-2 border-r border-dark-border/30 last:border-r-0">{{ Str::limit($cell, 20) }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="bg-dark-surface border border-dark-border rounded-2xl p-6">
            <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Matriks Korelasi (Pearson)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-center text-xs">
                    <thead>
                        <tr>
                            <th class="p-2"></th>
                            @foreach($correlationMatrix as $colName => $values)
                                <th class="p-2 text-slate-400 writing-mode-vertical rotate-180 min-w-[30px]">{{ $colName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($correlationMatrix as $rowName => $cols)
                        <tr>
                            <th class="p-2 text-left text-slate-400 font-normal whitespace-nowrap">{{ $rowName }}</th>
                            @foreach($cols as $colName => $val)
                                @php
                                    // Hitung warna background berdasarkan nilai korelasi (-1 s/d 1)
                                    // Merah = Negatif Kuat, Hijau = Positif Kuat, Hitam/Gelap = Netral (0)
                                    $bg = 'transparent';
                                    $text = 'text-slate-600';
                                    if ($val > 0.1) {
                                        $opacity = min($val, 1) * 0.8;
                                        $bg = "rgba(16, 185, 129, $opacity)"; // Emerald
                                        $text = $val > 0.5 ? 'text-white' : 'text-slate-300';
                                    } elseif ($val < -0.1) {
                                        $opacity = min(abs($val), 1) * 0.8;
                                        $bg = "rgba(244, 63, 94, $opacity)"; // Rose
                                        $text = abs($val) > 0.5 ? 'text-white' : 'text-slate-300';
                                    }
                                @endphp
                                <td class="p-1">
                                    <div class="w-full h-8 flex items-center justify-center rounded {{ $text }}" style="background-color: {{ $bg }}">
                                        @if($rowName != $colName) {{ number_format($val, 2) }} @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="bg-dark-surface border border-dark-border rounded-2xl p-6 flex flex-col">
                <h3 class="font-bold text-white mb-2 text-lg">🤖 Prediksi Linear</h3>
                <p class="text-sm text-slate-500 mb-6">Analisis pengaruh Variabel X terhadap Y.</p>
                
                <form action="{{ route('datasets.predict', $dataset->id) }}" method="POST" class="space-y-4 mb-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Penyebab (X)</label>
                        <select name="col_x" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm focus:border-accent-cyan focus:outline-none">
                            @foreach($stats as $col => $data)
                                @if($data['type'] == 'numeric') <option value="{{ $col }}">{{ $col }}</option> @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Akibat (Y)</label>
                        <select name="col_y" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm focus:border-accent-cyan focus:outline-none">
                            @foreach($stats as $col => $data)
                                @if($data['type'] == 'numeric') <option value="{{ $col }}" @selected($loop->iteration == 2)>{{ $col }}</option> @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-accent-cyan text-dark-bg font-bold rounded-lg hover:bg-cyan-400 transition-colors">
                        Mulai Prediksi
                    </button>
                </form>

                @if(session('prediction_result'))
                <div class="mt-auto bg-dark-bg p-4 rounded-xl border border-dark-border">
                    @php $res = session('prediction_result'); @endphp
                    <div class="text-xs text-slate-500 mb-2 font-mono">HASIL ANALISIS:</div>
                    <div class="text-2xl font-bold text-white mb-1">y = {{ number_format($res['m'], 2) }}x + {{ number_format($res['c'], 2) }}</div>
                    <div class="text-sm text-accent-cyan mb-3">Korelasi: {{ number_format($res['correlation'] * 100, 1) }}%</div>
                    
                    <div class="h-40 w-full relative">
                        <canvas id="predictionChart"></canvas>
                    </div>
                    <script>
                        const pCtx = document.getElementById('predictionChart').getContext('2d');
                        new Chart(pCtx, {
                            type: 'scatter',
                            data: {
                                datasets: [{
                                    label: 'Data',
                                    data: @json($res['points']),
                                    backgroundColor: '#06b6d4'
                                }, {
                                    type: 'line',
                                    label: 'Regresi',
                                    data: @json($res['line']),
                                    borderColor: '#f43f5e',
                                    borderWidth: 2,
                                    pointRadius: 0
                                }]
                            },
                            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: { color: '#334155' } } } }
                        });
                    </script>
                </div>
                @endif
            </div>

            <div class="bg-dark-surface border border-dark-border rounded-2xl p-6 flex flex-col">
                <h3 class="font-bold text-white mb-2 text-lg">🧩 K-Means Clustering</h3>
                <p class="text-sm text-slate-500 mb-6">Kelompokkan data berdasarkan kemiripan.</p>

                <form action="{{ route('datasets.cluster', $dataset->id) }}" method="POST" class="space-y-4 mb-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-1">Fitur 1</label>
                            <select name="col_x" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm">
                                @foreach($stats as $col => $data)
                                    @if($data['type'] == 'numeric') <option value="{{ $col }}">{{ $col }}</option> @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-1">Fitur 2</label>
                            <select name="col_y" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm">
                                @foreach($stats as $col => $data)
                                    @if($data['type'] == 'numeric') <option value="{{ $col }}" @selected($loop->iteration == 2)>{{ $col }}</option> @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Jumlah Cluster (K)</label>
                        <input type="number" name="k" value="3" min="2" max="5" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-2 bg-accent-purple text-white font-bold rounded-lg hover:bg-purple-600 transition-colors">
                        Mulai Clustering
                    </button>
                </form>

                @if(session('cluster_result'))
                <div class="mt-auto bg-dark-bg p-4 rounded-xl border border-dark-border">
                    @php $cRes = session('cluster_result'); @endphp
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-xs text-slate-500 font-mono">HASIL: {{ $cRes['iterations'] }} ITERASI</div>
                    </div>
                    <div class="h-48 w-full relative">
                        <canvas id="clusterChart"></canvas>
                    </div>
                    <script>
                        const cCtx = document.getElementById('clusterChart').getContext('2d');
                        const clusters = @json($cRes['clusters']);
                        const colors = ['#f43f5e', '#06b6d4', '#10b981', '#fbbf24', '#8b5cf6'];
                        
                        const datasets = Object.keys(clusters).map((key, index) => ({
                            label: 'Cluster ' + (parseInt(key) + 1),
                            data: clusters[key],
                            backgroundColor: colors[index % colors.length]
                        }));

                        new Chart(cCtx, {
                            type: 'scatter',
                            data: { datasets: datasets },
                            options: { 
                                maintainAspectRatio: false, 
                                plugins: { legend: { labels: { color: '#94a3b8', font: { size: 10 } } } }, 
                                scales: { x: { grid: { display: false } }, y: { grid: { color: '#334155' } } } 
                            }
                        });
                    </script>
                </div>
                @endif
            </div>

            <div class="bg-dark-surface border border-dark-border rounded-2xl p-6 flex flex-col">
                <h3 class="font-bold text-white mb-2 text-lg">🎯 Klasifikasi KNN</h3>
                <p class="text-sm text-slate-500 mb-6">Prediksi kategori data baru.</p>

                <form action="{{ route('datasets.classify', $dataset->id) }}" method="POST" class="space-y-4 mb-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Target (Label)</label>
                        <select name="target_col" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm">
                            @foreach($stats as $col => $data)
                                @if($data['type'] == 'categorical') <option value="{{ $col }}">{{ $col }}</option> @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="p-3 bg-dark-bg rounded-lg border border-dark-border max-h-40 overflow-y-auto custom-scrollbar">
                        <p class="text-xs text-slate-500 mb-2">Pilih Fitur & Input Nilai:</p>
                        @foreach($stats as $col => $data)
                            @if($data['type'] == 'numeric' && $loop->iteration <= 5)
                            <div class="mb-2">
                                <label class="flex items-center gap-2 text-xs text-slate-300 mb-1">
                                    <input type="checkbox" name="features[]" value="{{ $col }}" checked class="accent-accent-emerald">
                                    {{ $col }}
                                </label>
                                <input type="number" name="input_values[]" step="0.01" placeholder="Nilai..." required class="w-full bg-dark-input border border-dark-border rounded px-2 py-1 text-xs text-white">
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">Tetangga (K)</label>
                        <input type="number" name="k" value="3" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm">
                    </div>

                    <button type="submit" class="w-full py-2 bg-accent-emerald text-dark-bg font-bold rounded-lg hover:bg-emerald-400 transition-colors">
                        Prediksi Kategori
                    </button>
                </form>

                @if(session('classification_result'))
                <div class="mt-auto bg-dark-bg p-4 rounded-xl border border-dark-border">
                    @php $kRes = session('classification_result'); @endphp
                    <div class="text-center">
                        <div class="text-xs text-slate-500 mb-1">HASIL PREDIKSI:</div>
                        <div class="text-3xl font-extrabold text-accent-emerald mb-2">{{ $kRes['prediction'] }}</div>
                        <div class="text-xs text-slate-400 border-t border-dark-border pt-2 text-left">
                            <span class="block font-bold mb-1">Tetangga Terdekat:</span>
                            @foreach($kRes['neighbors'] as $n)
                                <div class="flex justify-between">
                                    <span>{{ $n['label'] }}</span>
                                    <span class="font-mono text-slate-600">{{ number_format($n['distance'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>

    </main>

</body>
</html>