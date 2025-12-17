<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Science Dashboard - {{ $dataset->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c7c7c7; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a0a0a0; }
    </style>
</head>
<body class="bg-slate-50 p-6 text-slate-800 font-sans min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">
        
        @if(session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm animate-fade-in-up">
            <strong class="font-bold">Berhasil!</strong> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 rounded shadow-sm animate-fade-in-up">
            <strong class="font-bold">Error!</strong> {{ session('error') }}
        </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-sm border border-slate-200 gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">{{ $dataset->name }}</h1>
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $dataset->status == 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ strtoupper($dataset->status) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-2 flex gap-4 font-medium">
                    <span class="flex items-center gap-1">📊 <b>{{ number_format($dataset->total_rows) }}</b> Rows</span>
                    <span class="flex items-center gap-1">📑 <b>{{ $dataset->total_columns }}</b> Columns</span>
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @php $totalMissing = collect($stats)->sum('missing'); @endphp

                @if($totalMissing > 0)
                    <form action="{{ route('datasets.clean', $dataset->id) }}" method="POST" onsubmit="return confirm('Sistem akan mengisi {{ $totalMissing }} sel kosong secara otomatis. Lanjutkan?');">
                        @csrf @method('PUT')
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-lg shadow-amber-500/30 animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            Fix Missing Values ({{ $totalMissing }})
                        </button>
                    </form>
                @else
                    <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 border border-emerald-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Data Clean & Ready
                    </div>
                @endif

                <a href="{{ route('dashboard') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2">
                    <span>&larr;</span> Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-teal-500 transition card-hover">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-slate-800">
                🔥 Correlation Matrix <span class="text-slate-400 font-normal text-sm">(Heatmap)</span>
                <span class="text-[10px] uppercase font-bold tracking-wider bg-teal-100 text-teal-700 px-2 py-1 rounded ml-auto">Feature Selection</span>
            </h2>
            
            @if(isset($correlationMatrix) && count($correlationMatrix) > 0)
                <div class="overflow-x-auto rounded-lg border border-slate-200 mb-4">
                    <table class="w-full text-xs text-center border-collapse">
                        <thead>
                            <tr>
                                <th class="p-3 bg-slate-50 border-b border-slate-200 text-left">Variables</th>
                                @foreach(array_keys($correlationMatrix) as $col)
                                    <th class="p-3 bg-slate-50 border-b border-l border-slate-200 font-bold text-slate-700">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($correlationMatrix as $rowCol => $cols)
                            <tr>
                                <td class="p-3 bg-slate-50 border-r border-b border-slate-200 font-bold text-slate-700 text-left">{{ $rowCol }}</td>
                                @foreach($cols as $colName => $val)
                                    @php
                                        $bg = 'bg-white'; $text = 'text-slate-800';
                                        if($val == 1) { $bg = 'bg-slate-100'; $text = 'text-slate-400'; }
                                        elseif($val > 0.7) { $bg = 'bg-emerald-500'; $text = 'text-white font-bold'; }
                                        elseif($val > 0.4) { $bg = 'bg-emerald-100'; $text = 'text-emerald-800'; }
                                        elseif($val < -0.7) { $bg = 'bg-rose-500'; $text = 'text-white font-bold'; }
                                        elseif($val < -0.4) { $bg = 'bg-rose-100'; $text = 'text-rose-800'; }
                                    @endphp
                                    <td class="p-3 border-b border-l border-slate-100 {{ $bg }} {{ $text }}">{{ number_format($val, 2) }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-teal-50 p-4 rounded-lg border border-teal-100 text-sm text-teal-900">
                    <h4 class="font-bold mb-2 flex items-center gap-2">💡 Insight Otomatis:</h4>
                    <ul class="list-disc list-inside space-y-1 text-slate-700">
                        @php $found = false; @endphp
                        @foreach($correlationMatrix as $colA => $cols)
                            @foreach($cols as $colB => $val)
                                @if($colA != $colB && abs($val) > 0.75 && $loop->parent->iteration < $loop->iteration)
                                    <li>Variabel <b>{{ $colA }}</b> & <b>{{ $colB }}</b> punya hubungan <b class="{{ $val > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $val > 0 ? 'KUAT POSITIF' : 'KUAT NEGATIF' }}</b> ({{ number_format($val*100, 0) }}%).</li>
                                    @php $found = true; @endphp
                                @endif
                            @endforeach
                        @endforeach
                        @if(!$found) <li class="text-slate-500 italic">Tidak ada korelasi linear yang sangat kuat. Gunakan Clustering atau cari fitur lain.</li> @endif
                    </ul>
                </div>
            @else
                <p class="text-slate-500 italic">Data numeric kurang dari 2 kolom, tidak bisa menghitung korelasi.</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-indigo-600 transition card-hover">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-slate-800">
                📈 AI Prediction <span class="text-slate-400 font-normal text-sm">(Regression)</span>
                <span class="text-[10px] uppercase font-bold tracking-wider bg-indigo-100 text-indigo-700 px-2 py-1 rounded ml-auto">Supervised</span>
            </h2>
            
            <form action="{{ route('datasets.predict', $dataset->id) }}" method="POST" class="flex flex-wrap gap-4 items-end mb-8 p-5 bg-slate-50 rounded-lg border border-slate-100">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Variable X (Penyebab)</label>
                    <select name="col_x" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                        @foreach($stats as $col => $data)
                            @if($data['type'] == 'numeric') <option value="{{ $col }}" {{ (session('prediction_result.col_x') == $col) ? 'selected' : '' }}>{{ $col }}</option> @endif
                        @endforeach
                    </select>
                </div>
                <div class="text-slate-300 pb-2 hidden md:block">➔</div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Variable Y (Akibat)</label>
                    <select name="col_y" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                        @foreach($stats as $col => $data)
                            @if($data['type'] == 'numeric') <option value="{{ $col }}" {{ (session('prediction_result.col_y') == $col) ? 'selected' : '' }}>{{ $col }}</option> @endif
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-md w-full md:w-auto text-sm">Run Prediction</button>
            </form>

            @if(session('prediction_result'))
                @php $res = session('prediction_result'); @endphp
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in-up">
                    <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 p-2">
                        <canvas id="predictionChart" height="220"></canvas>
                    </div>
                    <div class="bg-indigo-50 p-5 rounded-lg space-y-5 h-fit border border-indigo-100">
                        <div>
                            <h3 class="font-bold text-indigo-900 border-b border-indigo-200 pb-2 mb-3 text-xs uppercase tracking-wide">Model Matematika</h3>
                            <div class="bg-white p-3 rounded border border-indigo-100 text-center shadow-sm">
                                <p class="font-mono text-base text-slate-800">
                                    y = <span class="text-blue-600 font-bold">{{ number_format($res['m'], 4) }}</span>x + <span class="text-green-600 font-bold">{{ number_format($res['c'], 2) }}</span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-indigo-900 border-b border-indigo-200 pb-2 mb-3 text-xs uppercase tracking-wide">Korelasi</h3>
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-black {{ abs($res['correlation']) > 0.7 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ number_format($res['correlation'] * 100, 1) }}%
                                </span>
                                <div class="text-xs text-slate-600 leading-tight">
                                    Hubungan: <b>{{ abs($res['correlation']) > 0.7 ? 'Sangat Kuat' : (abs($res['correlation']) > 0.4 ? 'Cukup Kuat' : 'Lemah') }}</b>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-indigo-200 shadow-sm">
                            <p class="text-xs font-bold text-slate-500 uppercase mb-2">Simulator:</p>
                            <input type="number" id="inputX" placeholder="Input {{ $res['col_x'] }}" class="border border-slate-300 p-2 w-full text-sm rounded mb-2">
                            <div class="bg-slate-100 p-2 rounded border border-slate-200 text-center font-bold text-indigo-700 text-lg" id="outputY">-</div>
                        </div>
                    </div>
                </div>
                <script>
                    new Chart(document.getElementById('predictionChart'), {
                        type: 'scatter',
                        data: {
                            datasets: [{ label: 'Data', data: {!! json_encode($res['points']) !!}, backgroundColor: '#6366f1' },
                                       { type: 'line', label: 'Regresi', data: {!! json_encode($res['line']) !!}, borderColor: '#ef4444', borderWidth: 2, fill: false, pointRadius: 0 }]
                        },
                        options: { responsive: true, scales: { x: { title: {display: true, text: '{{ $res['col_x'] }}'} }, y: { title: {display: true, text: '{{ $res['col_y'] }}'} } } }
                    });
                    const m = {{ $res['m'] }}, c = {{ $res['c'] }};
                    document.getElementById('inputX').addEventListener('input', e => {
                        const val = parseFloat(e.target.value);
                        document.getElementById('outputY').innerText = isNaN(val) ? '-' : (m * val + c).toFixed(2);
                    });
                </script>
            @endif
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-pink-500 transition card-hover">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-slate-800">
                🧩 K-Means Clustering <span class="text-slate-400 font-normal text-sm">(Grouping)</span>
                <span class="text-[10px] uppercase font-bold tracking-wider bg-pink-100 text-pink-700 px-2 py-1 rounded ml-auto">Unsupervised</span>
            </h2>

            <form action="{{ route('datasets.cluster', $dataset->id) }}" method="POST" class="flex flex-wrap gap-4 items-end mb-8 p-5 bg-slate-50 rounded-lg border border-slate-100">
                @csrf
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Variable 1 (X)</label>
                    <select name="col_x" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm">
                        @foreach($stats as $col => $data)
                            @if($data['type'] == 'numeric') <option value="{{ $col }}">{{ $col }}</option> @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Variable 2 (Y)</label>
                    <select name="col_y" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm">
                        @foreach($stats as $col => $data)
                            @if($data['type'] == 'numeric') <option value="{{ $col }}" selected>{{ $col }}</option> @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Clusters (K)</label>
                    <input type="number" name="k" min="2" max="5" value="3" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm">
                </div>
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-md w-full md:w-auto text-sm">Run Clustering</button>
            </form>

            @if(session('cluster_result'))
                @php $clust = session('cluster_result'); @endphp
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in-up">
                    <div class="lg:col-span-2 bg-white border border-slate-200 p-2 rounded-lg">
                        <canvas id="clusterChart" height="220"></canvas>
                    </div>
                    <div class="bg-pink-50 p-5 rounded-lg border border-pink-100 h-fit">
                        <h3 class="font-bold text-pink-900 border-b border-pink-200 pb-2 mb-3 text-xs uppercase tracking-wide">Hasil Grouping</h3>
                        <p class="text-sm text-slate-700 mb-4">Data dibagi menjadi <b>{{ $clust['k'] }} Kelompok</b>:</p>
                        <div class="space-y-2">
                            @foreach($clust['clusters'] as $idx => $points)
                                <div class="bg-white p-3 rounded border border-slate-200 text-xs shadow-sm flex justify-between items-center">
                                    <span class="font-bold flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'][$idx] }}"></span>
                                        Cluster {{ $idx + 1 }}
                                    </span>
                                    <span class="text-slate-500 font-mono">{{ count($points) }} Items</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <script>
                    const clusterData = {!! json_encode($clust['clusters']) !!};
                    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];
                    const datasets = Object.keys(clusterData).map((key, i) => ({ label: 'Cluster '+(parseInt(key)+1), data: clusterData[key], backgroundColor: colors[i], pointRadius: 5 }));
                    new Chart(document.getElementById('clusterChart'), {
                        type: 'scatter', data: { datasets: datasets },
                        options: { responsive: true, plugins: { title: { display: true, text: 'K-Means Result' } }, scales: { x: { title: {display: true, text: '{{ $clust["col_x"] }}'} }, y: { title: {display: true, text: '{{ $clust["col_y"] }}'} } } }
                    });
                </script>
            @endif
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-emerald-500 transition card-hover">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-slate-800">
                🏷️ KNN Classification <span class="text-slate-400 font-normal text-sm">(Labeling)</span>
                <span class="text-[10px] uppercase font-bold tracking-wider bg-emerald-100 text-emerald-700 px-2 py-1 rounded ml-auto">Supervised</span>
            </h2>

            <form action="{{ route('datasets.classify', $dataset->id) }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-slate-50 rounded-lg border border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Target Label</label>
                        <select name="target_col" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm">
                            @foreach($stats as $col => $data) <option value="{{ $col }}">{{ $col }} ({{ $data['type'] }})</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tetangga (K)</label>
                        <input type="number" name="k" value="3" min="1" max="21" step="2" class="block w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm">
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg border border-slate-200">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-4">Input Data Baru (Features)</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($stats as $col => $data)
                            @if($data['type'] == 'numeric')
                            <div class="bg-slate-50 p-3 rounded border border-slate-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="checkbox" name="features[]" value="{{ $col }}" checked class="text-emerald-600 rounded">
                                    <span class="text-xs font-bold text-slate-700 truncate" title="{{ $col }}">{{ $col }}</span>
                                </div>
                                <input type="number" step="any" name="input_values[]" placeholder="Nilai..." class="w-full text-sm border-b border-slate-300 bg-transparent py-1 focus:outline-none focus:border-emerald-500">
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-bold transition shadow-md w-full md:w-auto text-sm">Klasifikasi Data</button>
            </form>

            @if(session('classification_result'))
                @php $res = session('classification_result'); @endphp
                <div class="mt-8 animate-fade-in-up bg-emerald-50 border border-emerald-200 rounded-xl p-6 flex flex-col md:flex-row gap-8">
                    <div class="flex-1 text-center md:text-left">
                        <h3 class="text-sm font-bold text-emerald-800 uppercase mb-4 tracking-wide">Hasil Prediksi</h3>
                        <div class="bg-white p-6 rounded-2xl border border-emerald-100 shadow-sm inline-block min-w-[200px]">
                            <span class="block text-xs text-slate-500 mb-2 font-medium">Kategori Terpilih:</span>
                            <span class="text-4xl font-black text-emerald-600">{{ $res['prediction'] }}</span>
                        </div>
                    </div>
                    <div class="flex-[2]">
                        <h3 class="text-sm font-bold text-emerald-800 uppercase mb-4 tracking-wide">Tetangga Terdekat</h3>
                        <div class="overflow-hidden rounded-lg border border-emerald-200 shadow-sm">
                            <table class="w-full text-xs text-left bg-white">
                                <thead class="bg-emerald-100 text-emerald-900 font-semibold uppercase">
                                    <tr><th class="p-3">Jarak</th><th class="p-3">Label</th><th class="p-3 text-center">Status</th></tr>
                                </thead>
                                <tbody class="divide-y divide-emerald-50">
                                    @foreach($res['neighbors'] as $n)
                                    <tr class="{{ $n['label'] == $res['prediction'] ? 'bg-emerald-50/50' : '' }}">
                                        <td class="p-3 font-mono text-slate-600">{{ number_format($n['distance'], 4) }}</td>
                                        <td class="p-3 font-bold text-slate-800">{{ $n['label'] }}</td>
                                        <td class="p-3 text-center">
                                            @if($n['label'] == $res['prediction']) <span class="px-2 py-1 rounded bg-emerald-200 text-emerald-800 font-bold">VOTE</span>
                                            @else <span class="text-slate-300">-</span> @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="pt-8">
            <h3 class="text-lg font-bold text-slate-700 uppercase tracking-wide border-l-4 border-slate-800 pl-4 mb-6">Descriptive Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($stats as $colName => $stat)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden transition card-hover h-full flex flex-col">
                    <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-slate-700 truncate max-w-[65%]" title="{{ $colName }}">{{ $colName }}</h3>
                        <span class="text-[10px] font-bold tracking-wider px-2 py-1 rounded border {{ $stat['type'] == 'numeric' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                            {{ strtoupper($stat['type']) }}
                        </span>
                    </div>
                    <div class="p-5 space-y-5 flex-1 flex flex-col justify-center">
                        @if($stat['missing'] > 0)
                        <div class="bg-amber-50 text-amber-700 text-xs px-3 py-2 rounded border border-amber-200 flex justify-between items-center">
                            <span class="font-bold flex items-center gap-1">⚠️ Missing</span>
                            <span>{{ $stat['missing'] }} ({{ round(($stat['missing']/$stat['count'])*100, 1) }}%)</span>
                        </div>
                        @endif

                        @if($stat['type'] == 'numeric')
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div><p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Mean</p><p class="font-bold text-slate-800 text-xl">{{ number_format($stat['mean'], 2) }}</p></div>
                                <div><p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Std. Dev</p><p class="font-bold text-slate-800 text-xl">{{ number_format($stat['std_dev'], 2) }}</p></div>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-[10px] text-slate-400 font-bold"><span>MIN: {{ $stat['min'] }}</span><span>MAX: {{ $stat['max'] }}</span></div>
                                <div class="bg-slate-100 rounded-full h-2 w-full relative overflow-hidden"><div class="absolute left-0 top-0 h-full bg-blue-500 w-full opacity-60"></div></div>
                            </div>
                            <div class="pt-4 border-t border-slate-50 grid grid-cols-3 text-xs text-center divide-x divide-slate-100">
                                <div><span class="block text-slate-400 text-[10px] uppercase">Q1</span><b class="text-slate-700">{{ number_format($stat['q1'], 2) }}</b></div>
                                <div><span class="block text-blue-600 text-[10px] uppercase font-bold">Median</span><b class="text-blue-700">{{ number_format($stat['median'], 2) }}</b></div>
                                <div><span class="block text-slate-400 text-[10px] uppercase">Q3</span><b class="text-slate-700">{{ number_format($stat['q3'], 2) }}</b></div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="flex justify-between items-center"><span class="text-xs text-slate-500 font-medium">Unique Values</span><span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $stat['unique_count'] }}</span></div>
                                <div class="pt-3 border-t border-slate-50">
                                    <p class="text-[10px] font-bold text-slate-400 mb-3 uppercase tracking-wider">Top Categories</p>
                                    <ul class="text-xs space-y-2">
                                        @foreach($stat['top_categories'] as $cat => $count)
                                        <li class="flex justify-between items-center">
                                            <span class="truncate w-2/3 text-slate-700 font-medium" title="{{ $cat }}">{{ $cat ?: '(Empty)' }}</span>
                                            <div class="flex items-center gap-2"><div class="w-12 bg-slate-100 rounded-full h-1.5 overflow-hidden"><div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ ($count / $stat['count']) * 100 }}%"></div></div><span class="font-bold text-slate-600">{{ $count }}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-8 mb-12">
            <h2 class="text-lg font-bold mb-4 flex justify-between items-center">
                <span>Raw Data Preview <span class="text-sm font-normal text-slate-500 ml-2">(First 50 Rows)</span></span>
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Back to Top &uarr;</button>
            </h2>
            <div class="overflow-x-auto border border-slate-200 rounded-lg max-h-[500px] overflow-y-auto">
                <table class="w-full text-sm text-left border-collapse relative">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-bold sticky top-0 shadow-sm z-10">
                        <tr>@foreach($header as $h) <th class="bg-slate-50 border-b border-slate-200 px-6 py-4 whitespace-nowrap">{{ $h }}</th> @endforeach</tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($previewRows as $index => $r)
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50/30' }} hover:bg-indigo-50/50 transition-colors">
                            @foreach($r as $cell) <td class="px-6 py-3 truncate max-w-[200px] text-slate-700">{{ $cell }}</td> @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-slate-400 mt-4 text-center">Menampilkan 50 baris pertama untuk pratinjau cepat.</p>
        </div>
    </div>
</body>
</html>