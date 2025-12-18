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
        .upload-area:hover {
            background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='16' ry='16' stroke='%236366F1FF' stroke-width='2' stroke-dasharray='12%2c 12' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
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
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased overflow-hidden selection:bg-accent-indigo selection:text-white">

    <div class="flex h-screen w-full relative">
        
        <div class="absolute inset-0 bg-grid-pattern opacity-20 pointer-events-none z-0"></div>

        <div class="absolute top-6 left-8 z-50">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors group">
                <div class="w-8 h-8 rounded-full bg-dark-surface border border-dark-border flex items-center justify-center group-hover:border-accent-indigo transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                <span class="font-medium text-sm">Kembali ke Dashboard</span>
            </a>
        </div>

        <main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-6xl mx-auto">

            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-accent-indigo/10 border border-accent-indigo/20 mb-4 shadow-[0_0_30px_-5px_rgba(99,102,241,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-accent-indigo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2 tracking-tight">Deep Learning Studio</h1>
                <p class="text-slate-400 text-lg">Train Neural Networks, CNN, & RNN tanpa menulis kode Python.</p>
            </div>

            <div class="w-full bg-dark-surface border border-dark-border rounded-3xl p-1 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-accent-indigo to-transparent opacity-50"></div>

                <div class="bg-dark-bg/50 rounded-[22px] p-6 md:p-8 backdrop-blur-sm">
                    
                    <form action="{{ route('deep_learning.process') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="trainingForm">
                        @csrf

                        <div class="lg:col-span-1 space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-white mb-2">1. Upload Dataset</label>
                                <p class="text-xs text-slate-500 mb-3">Format: .zip (Images), .csv (Tabular)</p>
                                
                                <div class="upload-area relative group w-full h-48 rounded-xl bg-dark-input border-2 border-dashed border-dark-border hover:border-accent-indigo transition-all cursor-pointer flex flex-col items-center justify-center">
                                    <input type="file" name="dataset" id="datasetInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                                    
                                    <div class="w-12 h-12 rounded-full bg-dark-surface flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                                        <svg class="w-6 h-6 text-slate-400 group-hover:text-accent-indigo transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <span id="uploadText" class="text-sm font-medium text-slate-300 group-hover:text-white">Click to Upload</span>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-white mb-2">2. Pilih Arsitektur Model</label>
                                <div class="space-y-3">
                                    <label class="flex items-center justify-between p-3 rounded-xl bg-dark-input border border-dark-border cursor-pointer hover:border-accent-indigo transition-all">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="model_type" value="CNN" checked class="text-accent-indigo bg-dark-bg border-slate-600">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-white">CNN (Convolutional)</span>
                                                <span class="text-xs text-slate-500">Gambar/Visi/Tabular 1D</span>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="flex items-center justify-between p-3 rounded-xl bg-dark-input border border-dark-border cursor-pointer hover:border-accent-indigo transition-all">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="model_type" value="ANN" class="text-accent-indigo bg-dark-bg border-slate-600">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-white">ANN (Basic MLP)</span>
                                                <span class="text-xs text-slate-500">Data Tabular</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-6 flex flex-col">
                            <div>
                                <label class="block text-sm font-bold text-white mb-2">3. Hyperparameters</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">Epochs</label>
                                        <input type="number" name="epochs" value="5" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm focus:border-accent-indigo focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">Learning Rate</label>
                                        <select name="learning_rate" class="w-full bg-dark-input border border-dark-border rounded-lg px-3 py-2 text-white text-sm focus:border-accent-indigo focus:outline-none">
                                            <option value="0.001">0.001</option>
                                            <option value="0.01">0.01</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-6">
                                <button type="submit" onclick="showLoading()" class="w-full group relative flex items-center justify-center py-3.5 px-6 border border-transparent rounded-xl text-white bg-accent-indigo hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-900/30">
                                    <span class="font-bold tracking-wide">Mulai Training Model</span>
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </main>
    </div>

    <script>
        // Update teks saat file dipilih
        function updateFileName(input) {
            const fileName = input.files[0]?.name;
            if (fileName) {
                document.getElementById('uploadText').innerText = fileName;
                document.getElementById('uploadText').classList.add('text-accent-indigo');
            }
        }

        // Tampilkan Loading saat submit
        function showLoading() {
            Swal.fire({
                title: 'Sedang Mengupload...',
                text: 'Mohon tunggu, dataset sedang diproses.',
                icon: 'info',
                showConfirmButton: false,
                background: '#151F32',
                color: '#fff',
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        }

        // --- POPUP SUKSES DENGAN GRAFIK PLOTTING ---
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
                            <p class="text-[10px] text-center text-slate-500">
                                File ini berisi "Otak AI" yang sudah pintar. <br> 
                                Bisa digunakan untuk prediksi di masa depan.
                            </p>
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