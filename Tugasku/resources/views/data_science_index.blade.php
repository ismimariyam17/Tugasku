<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Science Workspace - Tugasku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: { bg: '#0B1120', surface: '#151F32', border: '#1E293B', input: '#0F172A' },
                        accent: { cyan: '#06b6d4', emerald: '#10b981', rose: '#f43f5e' }
                    }
                }
            }
        }
    </script>
    <style>
        .glass { background: rgba(21, 31, 50, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0B1120; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased min-h-screen">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-20 bg-dark-surface border-r border-dark-border flex flex-col items-center py-6 z-20">
            <a href="{{ url('/') }}" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 mb-8 hover:scale-110 transition-transform" title="Kembali ke Dashboard">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </a>
            
            <div class="space-y-4 flex flex-col items-center w-full">
                <div class="w-10 h-10 rounded-xl bg-accent-cyan/20 text-accent-cyan flex items-center justify-center border border-accent-cyan/50 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
            </div>
        </aside>

        <main class="flex-1 flex flex-col relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-accent-cyan/5 to-transparent pointer-events-none"></div>

            <header class="h-20 flex items-center justify-between px-8 border-b border-dark-border bg-dark-bg/80 backdrop-blur-md z-10">
                <div>
                    <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                        Data Science <span class="text-accent-cyan">Workspace</span>
                    </h1>
                    <p class="text-xs text-slate-500">Kelola dataset dan mulai analisis otomatis</p>
                </div>
                
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-accent-cyan hover:bg-cyan-400 text-dark-bg font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Upload Dataset Baru</span>
                </button>
            </header>

            <div class="flex-1 overflow-y-auto p-8 relative z-0">

                @if(session('success'))
                <div class="mb-6 bg-accent-emerald/10 border border-accent-emerald/20 text-accent-emerald px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-accent-rose/10 border border-accent-rose/20 text-accent-rose px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($datasets->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($datasets as $dataset)
                        <a href="{{ route('datasets.show', $dataset->id) }}" class="group relative bg-dark-surface rounded-2xl border border-dark-border hover:border-accent-cyan/50 p-6 transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-900/10 block">
                            <div class="absolute top-4 right-4">
                                @if($dataset->status == 'completed')
                                    <span class="flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    </span>
                                @else
                                    <span class="w-3 h-3 rounded-full bg-yellow-500 block"></span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-lg bg-dark-input flex items-center justify-center text-slate-400 group-hover:bg-accent-cyan group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="overflow-hidden">
                                    <h3 class="font-bold text-lg text-white truncate group-hover:text-accent-cyan transition-colors">{{ $dataset->name }}</h3>
                                    <p class="text-xs text-slate-500">{{ $dataset->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-400 mb-4 bg-dark-input/50 p-3 rounded-lg">
                                <div>
                                    <span class="block text-slate-500">Baris</span>
                                    <span class="font-mono text-white">{{ number_format($dataset->total_rows) }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-500">Kolom</span>
                                    <span class="font-mono text-white">{{ $dataset->total_columns }}</span>
                                </div>
                            </div>

                            <div class="flex items-center text-sm font-semibold text-accent-cyan gap-1 group-hover:gap-2 transition-all">
                                Buka Analisis <span>→</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-96 text-center border-2 border-dashed border-dark-border rounded-3xl bg-dark-surface/30">
                        <div class="w-20 h-20 bg-dark-input rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Belum ada Dataset</h3>
                        <p class="text-slate-500 max-w-sm mb-6">Upload file CSV pertama kamu untuk mulai menggunakan fitur otomatisasi data science.</p>
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-6 py-2 bg-accent-cyan text-dark-bg font-bold rounded-full hover:scale-105 transition-transform">
                            Upload Sekarang
                        </button>
                    </div>
                @endif
                
            </div>
        </main>
    </div>

    <div id="uploadModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm transition-opacity" onclick="this.parentElement.classList.add('hidden')"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-dark-surface border border-dark-border text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-dark-surface px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-accent-cyan/10 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-accent-cyan" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-white" id="modal-title">Upload Dataset Baru</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-400 mb-4">Pilih file CSV dari komputer kamu. Sistem akan otomatis menganalisis struktur data.</p>
                                    
                                    <div class="flex items-center justify-center w-full">
                                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-600 border-dashed rounded-xl cursor-pointer bg-dark-input hover:bg-dark-input/80 hover:border-accent-cyan transition-all group">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-3 text-slate-400 group-hover:text-accent-cyan" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                </svg>
                                                <p class="text-xs text-slate-400"><span class="font-bold">Klik untuk upload</span></p>
                                                <p class="text-xs text-slate-500 mt-1">CSV (Max. 10MB)</p>
                                            </div>
                                            <input id="dropzone-file" name="file_csv" type="file" class="hidden" accept=".csv" required />
                                        </label>
                                    </div>
                                    <p id="file-name" class="text-accent-cyan text-xs mt-2 text-center h-4"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-dark-input/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-accent-cyan px-3 py-2 text-sm font-semibold text-dark-bg shadow-sm hover:bg-cyan-400 sm:ml-3 sm:w-auto">Proses Data</button>
                        <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-dark-surface ring-1 ring-inset ring-slate-600 px-3 py-2 text-sm font-semibold text-slate-300 shadow-sm hover:bg-dark-input sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('dropzone-file');
        const fileNameDisplay = document.getElementById('file-name');

        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                fileNameDisplay.textContent = 'File terpilih: ' + fileName;
            }
        });
    </script>
</body>
</html>