<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deep Learning Studio - Tugasku</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0B1120',      
                            surface: '#151F32', 
                            border: '#1E293B',  
                            input: '#0F172A',   
                        },
                        accent: {
                            indigo: '#6366f1',
                            purple: '#8b5cf6', 
                            cyan: '#06b6d4',
                        }
                    },
                    fontFamily: {
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', "Liberation Mono", "Courier New", 'monospace'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(21, 31, 50, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .bg-grid-pattern {
            background-image: linear-gradient(to right, #1e293b 1px, transparent 1px),
                              linear-gradient(to bottom, #1e293b 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: linear-gradient(to bottom, transparent, black, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent 10%, black 40%, black 70%, transparent 90%);
        }
        /* Style khusus SweetAlert agar cocok dengan tema Dark */
        div:where(.swal2-container) div:where(.swal2-popup) {
            background: #151F32 !important;
            border: 1px solid #334155;
            color: #e2e8f0;
        }
        div:where(.swal2-icon).swal2-success {
            border-color: #6366f1;
            color: #6366f1;
        }
        div:where(.swal2-icon).swal2-info {
            border-color: #06b6d4;
            color: #06b6d4;
        }
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased selection:bg-accent-indigo selection:text-white">

    <div class="relative min-h-screen w-full flex flex-col">
        
        <div class="fixed inset-0 bg-grid-pattern opacity-20 pointer-events-none z-0"></div>

        <div class="absolute top-6 left-8 z-50">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors group">
                <div class="w-8 h-8 rounded-full bg-dark-surface border border-dark-border flex items-center justify-center group-hover:border-accent-indigo transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                <span class="font-medium text-sm">Kembali ke Dashboard</span>
            </a>
        </div>

        <main class="flex-1 flex flex-col items-center justify-center py-20 px-6 relative z-10 w-full max-w-7xl mx-auto">

            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-accent-indigo/10 border border-accent-indigo/20 mb-4 shadow-[0_0_30px_-5px_rgba(99,102,241,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-accent-indigo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-2 tracking-tight">Deep Learning Studio</h1>
                <p class="text-slate-400">Train Model & Lakukan Prediksi Tanpa Coding.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full mb-10">

                <div class="w-full bg-dark-surface border border-dark-border rounded-3xl p-1 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-accent-indigo to-transparent opacity-50"></div>
                    <div class="bg-dark-bg/50 rounded-[22px] p-6 backdrop-blur-sm h-full flex flex-col">
                        
                        <div class="flex items-center gap-3 mb-6 border-b border-dark-border pb-4">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold">1</div>
                            <h3 class="text-xl font-bold text-white">Training Mode</h3>
                        </div>

                        <form action="{{ route('deep_learning.process') }}" method="POST" enctype="multipart/form-data" class="space-y-5 flex-1 flex flex-col">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Dataset Training (.csv)</label>
                                <input type="file" name="dataset" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent-indigo file:text-white hover:file:bg-indigo-500 cursor-pointer bg-dark-input border border-dark-border rounded-lg p-1">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Arsitektur</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center justify-center p-3 rounded-xl bg-dark-input border border-dark-border cursor-pointer hover:border-accent-indigo transition-all has-[:checked]:border-accent-indigo has-[:checked]:bg-indigo-500/10">
                                        <input type="radio" name="model_type" value="CNN" checked class="hidden">
                                        <span class="text-sm font-semibold text-white">CNN</span>
                                    </label>
                                    <label class="flex items-center justify-center p-3 rounded-xl bg-dark-input border border-dark-border cursor-pointer hover:border-accent-indigo transition-all has-[:checked]:border-accent-indigo has-[:checked]:bg-indigo-500/10">
                                        <input type="radio" name="model_type" value="ANN" class="hidden">
                                        <span class="text-sm font-semibold text-white">ANN</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-slate-500 block mb-1">Epochs</label>
                                    <input type="number" name="epochs" value="10" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm focus:border-accent-indigo focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 block mb-1">Learning Rate</label>
                                    <select name="learning_rate" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm focus:border-accent-indigo focus:outline-none">
                                        <option value="0.001">0.001</option>
                                        <option value="0.01">0.01</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-auto pt-4">
                                <button type="submit" onclick="showLoading()" class="w-full py-3 px-6 rounded-xl text-white bg-accent-indigo hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-900/30 font-bold tracking-wide">
                                    Mulai Training
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="w-full bg-dark-surface border border-dark-border rounded-3xl p-1 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-accent-cyan to-transparent opacity-50"></div>
                    
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>

                    <div class="bg-dark-bg/50 rounded-[22px] p-6 backdrop-blur-sm h-full flex flex-col">
                        
                        <div class="flex items-center gap-3 mb-6 border-b border-dark-border pb-4">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400 font-bold">2</div>
                            <h3 class="text-xl font-bold text-white">Prediction Mode</h3>
                        </div>

                        <form action="{{ route('deep_learning.predict') }}" method="POST" enctype="multipart/form-data" class="space-y-5 flex-1 flex flex-col">
                            @csrf
                            
                            <div class="bg-cyan-900/10 border border-cyan-500/20 rounded-lg p-4 mb-4">
                                <p class="text-xs text-cyan-200 leading-relaxed">
                                    <strong class="block mb-1 text-cyan-400">Cara Kerja:</strong>
                                    Upload file CSV data baru (tanpa kolom target). Sistem akan menggunakan "Otak AI" hasil training terakhir untuk menebak hasilnya.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Data Baru (.csv)</label>
                                <input type="file" name="dataset_predict" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent-cyan file:text-white hover:file:bg-cyan-600 cursor-pointer bg-dark-input border border-dark-border rounded-lg p-1">
                            </div>

                            <div class="mt-auto pt-4">
                                <button type="submit" onclick="showLoading()" class="w-full py-3 px-6 rounded-xl text-white bg-emerald-600 hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/30 font-bold tracking-wide flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Prediksi Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="w-full mb-10">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Eksperimen
                </h3>

                <div class="bg-dark-surface border border-dark-border rounded-2xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="bg-dark-input text-slate-200 uppercase font-bold border-b border-dark-border">
                                <tr>
                                    <th class="px-6 py-4">Waktu</th>
                                    <th class="px-6 py-4">Model</th>
                                    <th class="px-6 py-4">Epochs</th>
                                    <th class="px-6 py-4">Akurasi</th>
                                    <th class="px-6 py-4">Loss</th>
                                    <th class="px-6 py-4 text-center">Grafik</th>
                                    <th class="px-6 py-4 text-center">Download</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-border">
                                @if(isset($histories))
                                    @forelse($histories as $history)
                                    <tr class="hover:bg-dark-input/50 transition-colors">
                                        <td class="px-6 py-4">{{ $history->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-xs font-bold {{ $history->model_type == 'CNN' ? 'bg-accent-indigo/20 text-accent-indigo' : 'bg-accent-purple/20 text-accent-purple' }}">
                                                {{ $history->model_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $history->epochs }}</td>
                                        <td class="px-6 py-4 font-bold text-emerald-400">{{ $history->accuracy }}%</td>
                                        <td class="px-6 py-4">{{ $history->loss }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($history->plot_file)
                                                <a href="{{ route('display.plot', $history->plot_file) }}" target="_blank" class="text-accent-cyan hover:underline text-xs">Lihat</a>
                                            @else - @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('download.model', 'model_latest.h5') }}" class="text-slate-300 hover:text-white">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-500 italic">Belum ada riwayat training.</td>
                                    </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-500 italic">
                                            Fitur history belum aktif.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        // Tampilkan Loading
        function showLoading() {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar.',
                icon: 'info',
                showConfirmButton: false,
                background: '#151F32',
                color: '#fff',
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        }

        // --- POPUP TRAINING SUKSES ---
        @if(session('success'))
            Swal.fire({
                title: 'Training & Saving Berhasil!',
                html: `
                    <div class="text-left text-sm space-y-3">
                        <div class="flex justify-between items-center bg-dark-bg p-3 rounded-lg border border-dark-border">
                            <span class="text-slate-400">Akurasi:</span>
                            <span class="font-bold text-accent-indigo text-lg">{{ session('training_result')['accuracy'] ?? 0 }}%</span>
                        </div>
                        
                        @if(session('plot_url'))
                            <div class="p-1 bg-white rounded-lg">
                                <img src="{{ session('plot_url') }}" alt="Training Graph" class="w-full rounded-md shadow-sm">
                            </div>
                        @endif

                        @if(session('model_url'))
                            <a href="{{ session('model_url') }}" class="flex items-center justify-center gap-2 w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Model (.h5)
                            </a>
                        @endif
                    </div>
                `,
                icon: 'success',
                background: '#151F32',
                color: '#fff',
                width: '600px', 
                confirmButtonColor: '#6366f1',
                confirmButtonText: 'Tutup'
            });
        @endif

        // --- POPUP PREDIKSI SUKSES (DENGAN CONFIDENCE SCORE) ---
        @if(session('prediction_data'))
            // Ambil data dari Controller
            const predictions = @json(session('prediction_data')); 
            let tableRows = '';

            predictions.forEach((item, index) => {
                // Logika Warna: Hijau jika yakin > 80%, Kuning jika ragu, Merah jika sangat ragu
                let colorClass = 'bg-emerald-500';
                if(item.confidence < 60) colorClass = 'bg-red-500';
                else if(item.confidence < 80) colorClass = 'bg-yellow-500';

                tableRows += `
                    <tr class="border-b border-slate-700/50">
                        <td class="py-2 px-3 text-slate-400">Data #${index + 1}</td>
                        <td class="py-2 px-3 font-bold text-white text-center">${item.class}</td>
                        <td class="py-2 px-3 w-full">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="${colorClass} h-full rounded-full" style="width: ${item.confidence}%"></div>
                                </div>
                                <span class="text-xs text-slate-300 w-10 text-right">${item.confidence}%</span>
                            </div>
                        </td>
                    </tr>
                `;
            });

            Swal.fire({
                title: '🔍 Hasil Analisis AI',
                html: `
                    <div class="bg-dark-bg p-1 rounded-lg border border-dark-border mt-2">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase bg-slate-800 text-slate-400">
                                <tr>
                                    <th class="py-2 px-3">Input</th>
                                    <th class="py-2 px-3 text-center">Prediksi</th>
                                    <th class="py-2 px-3">Tingkat Keyakinan (Confidence)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                        <p class="text-[10px] text-slate-500 mt-3 text-center italic">*Menampilkan 10 data pertama</p>
                    </div>
                `,
                icon: 'success', 
                background: '#151F32',
                color: '#fff',
                width: '600px',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Keren!'
            });
        @endif

        // --- POPUP ERROR ---
        @if($errors->any())
             Swal.fire({
                title: 'Error!',
                html: '<ul class="text-left text-sm">@foreach($errors->all() as $error)<li class="mb-1 text-rose-400">• {{ $error }}</li>@endforeach</ul>',
                icon: 'error',
                background: '#151F32',
                color: '#fff',
                confirmButtonColor: '#f43f5e'
            });
        @endif
    </script>

</body>
</html>