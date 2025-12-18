<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugasku - AI Automation Workspace</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0B1120',      // Latar belakang utama (sangat gelap)
                            surface: '#151F32', // Kartu/Panel
                            border: '#1E293B',  // Garis batas
                            input: '#0F172A',   // Kolom input
                        },
                        accent: {
                            cyan: '#06b6d4',    // Cyan neon (Data Science)
                            purple: '#8b5cf6',  // Ungu neon (Deep Learning/Web)
                            rose: '#f43f5e',    // Merah neon (Error/Delete)
                            emerald: '#10b981', // Hijau neon (Success)
                            indigo: '#6366f1',  // Indigo (Deep Learning)
                        }
                    },
                    fontFamily: {
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', "Liberation Mono", "Courier New", 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Efek Glassmorphism */
        .glass {
            background: rgba(21, 31, 50, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0B1120; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Grid Pattern Background */
        .bg-grid-pattern {
            background-image: linear-gradient(to right, #1e293b 1px, transparent 1px),
                              linear-gradient(to bottom, #1e293b 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: linear-gradient(to bottom, transparent, black, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent 10%, black 40%, black 70%, transparent 90%);
        }

        /* Animasi Pulse Glow */
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 5px rgba(6, 182, 212, 0.2); }
            50% { box-shadow: 0 0 20px rgba(6, 182, 212, 0.6); }
        }
        .animate-glow { animation: glow 3s infinite; }
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased overflow-hidden selection:bg-accent-cyan selection:text-white">

    <div x-data="{ sidebarOpen: true, notificationsOpen: false }" class="flex h-screen w-full">

        <aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="flex-shrink-0 bg-dark-surface border-r border-dark-border flex flex-col transition-all duration-300 relative z-30">
            
            <div class="h-20 flex items-center justify-center border-b border-dark-border/50">
                <div class="flex items-center gap-3 overflow-hidden px-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-accent-cyan to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <div :class="sidebarOpen ? 'block' : 'hidden'" class="font-bold text-xl text-white tracking-wide transition-opacity duration-200">
                        AUTO<span class="text-accent-cyan">TASK</span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 space-y-1 px-3 custom-scrollbar">
                
                <div :class="sidebarOpen ? 'block' : 'hidden'" class="px-4 mb-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Workspace</div>
                
                <a href="#" class="group flex items-center px-3 py-3 text-white bg-accent-cyan/10 border border-accent-cyan/20 rounded-xl relative overflow-hidden">
                    <div class="absolute inset-0 bg-accent-cyan/10 translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-300"></div>
                    <svg class="w-6 h-6 mr-3 text-accent-cyan z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium z-10">Dashboard Alat</span>
                </a>

                <a href="#" class="group flex items-center px-3 py-3 text-slate-400 hover:text-white hover:bg-dark-input rounded-xl transition-all">
                    <svg class="w-6 h-6 mr-3 group-hover:text-accent-purple transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium">Project Tersimpan</span>
                </a>

                <a href="#" class="group flex items-center px-3 py-3 text-slate-400 hover:text-white hover:bg-dark-input rounded-xl transition-all">
                    <svg class="w-6 h-6 mr-3 group-hover:text-accent-rose transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium">Hasil Analisis</span>
                </a>

                <div class="my-6 border-t border-dark-border"></div>
                
                <div :class="sidebarOpen ? 'block' : 'hidden'" class="px-4 mb-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Server Status</div>
                
                <div :class="sidebarOpen ? 'block' : 'hidden'" class="px-4 py-2">
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1 text-slate-400">
                            <span>CPU Load</span>
                            <span class="text-accent-cyan">12%</span>
                        </div>
                        <div class="w-full bg-dark-bg rounded-full h-1.5">
                            <div class="bg-accent-cyan h-1.5 rounded-full" style="width: 12%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                         <div class="flex justify-between text-xs mb-1 text-slate-400">
                             <span>Memory</span>
                             <span class="text-accent-purple">45%</span>
                         </div>
                         <div class="w-full bg-dark-bg rounded-full h-1.5">
                             <div class="bg-accent-purple h-1.5 rounded-full" style="width: 45%"></div>
                         </div>
                     </div>
                </div>

            </nav>

            <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-24 bg-dark-surface border border-dark-border text-slate-400 hover:text-white p-1 rounded-full shadow-lg z-50">
                <svg :class="sidebarOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <div class="p-4 border-t border-dark-border bg-dark-surface/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center border border-slate-600">
                        <span class="font-bold text-sm text-white">IM</span>
                    </div>
                    <div :class="sidebarOpen ? 'block' : 'hidden'" class="flex flex-col overflow-hidden">
                        <span class="text-sm font-bold text-white truncate">User</span>
                        <span class="text-xs text-slate-500 truncate">Pro Plan Active</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 flex flex-col relative overflow-hidden bg-dark-bg">
            
            <div class="absolute inset-0 bg-grid-pattern opacity-20 pointer-events-none z-0"></div>
            
            <header class="h-20 glass flex items-center justify-between px-8 z-20 sticky top-0">
                <div class="w-full max-w-2xl relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2.5 border border-dark-border rounded-xl leading-5 bg-dark-input/50 text-slate-300 placeholder-slate-600 focus:outline-none focus:bg-dark-input focus:border-accent-cyan/50 focus:ring-1 focus:ring-accent-cyan/50 sm:text-sm transition-all" placeholder="Ketik perintah... (contoh: 'Analisis Data', 'Buat Laporan', 'Deploy')">
                </div>

                <div class="flex items-center gap-4 ml-4">
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-accent-emerald/10 border border-accent-emerald/20 rounded-full">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-medium text-emerald-400">System Ready</span>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto z-10 p-8 custom-scrollbar">
                
                <div class="mb-12 relative">
                    <div class="absolute -top-20 -left-20 w-72 h-72 bg-accent-cyan/20 rounded-full blur-[100px] pointer-events-none"></div>
                    <div class="absolute -top-20 -right-20 w-72 h-72 bg-accent-purple/20 rounded-full blur-[100px] pointer-events-none"></div>

                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">
                        Pilih <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-cyan to-blue-500">Tools Otomatisasi</span> Kamu
                    </h1>
                    <p class="text-slate-400 text-lg max-w-2xl leading-relaxed">
                        Sistem AI kami siap membantu mengerjakan tugas secara instan. Cukup upload file mentah (CSV, PDF, JSON), dan biarkan algoritma yang bekerja.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">

                    <a href="{{ route('data_science.index') }}" class="group relative col-span-1 md:col-span-2 bg-dark-surface rounded-2xl p-1 border border-dark-border hover:border-accent-cyan/50 transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-900/20 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-gradient-to-br from-accent-cyan/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
                        <div class="h-full bg-dark-bg/50 rounded-xl p-6 relative overflow-hidden backdrop-blur-sm">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-accent-cyan to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5 5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                </div>
                                <span class="bg-accent-cyan/10 text-accent-cyan text-xs font-bold px-3 py-1 rounded-full border border-accent-cyan/20 animate-pulse">
                                    READY TO USE
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-accent-cyan transition-colors">Data Science Automator</h3>
                            <p class="text-slate-400 mb-6 line-clamp-2">
                                Upload file CSV/Excel dataset kamu. Sistem akan otomatis melakukan Cleaning, Regresi, Clustering (K-Means), dan Klasifikasi tanpa perlu coding manual.
                            </p>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-2 py-1 rounded bg-dark-surface border border-dark-border text-xs text-slate-400">Auto Cleaning</span>
                                <span class="px-2 py-1 rounded bg-dark-surface border border-dark-border text-xs text-slate-400">Prediction</span>
                                <span class="px-2 py-1 rounded bg-dark-surface border border-dark-border text-xs text-slate-400">K-Means</span>
                            </div>
                            <div class="flex items-center text-accent-cyan font-semibold text-sm group-hover:gap-2 transition-all">
                                Mulai Analisis Sekarang <span class="ml-1">→</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('deep_learning.index') }}" class="group relative bg-dark-surface rounded-2xl border border-dark-border p-6 hover:border-accent-indigo/50 transition-all hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-900/20 block">
                        <div class="absolute top-4 right-4">
                             <span class="w-2 h-2 rounded-full bg-accent-indigo animate-pulse"></span>
                        </div>
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl bg-dark-input flex items-center justify-center border border-dark-border text-accent-indigo group-hover:bg-accent-indigo group-hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-accent-indigo transition-colors">Deep Learning</h3>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">
                            Train Neural Networks, CNN, dan arsitektur AI tingkat lanjut dengan dataset visual atau teks kompleks.
                        </p>
                        <div class="flex items-center text-accent-indigo font-semibold text-sm group-hover:gap-2 transition-all">
                            Buka Studio <span class="ml-1">→</span>
                        </div>
                    </a>

                    <a href="{{ route('code_generator.index') }}" class="group relative bg-dark-surface rounded-2xl border border-dark-border p-6 hover:border-pink-500/50 transition-all hover:-translate-y-1 hover:shadow-2xl hover:shadow-pink-900/20 block">
                        <div class="absolute top-4 right-4">
                             <span class="w-2 h-2 rounded-full bg-pink-500 animate-pulse"></span>
                        </div>
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl bg-dark-input flex items-center justify-center border border-dark-border text-pink-500 group-hover:bg-pink-500 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2 group-hover:text-pink-400 transition-colors">Code Generator</h3>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">
                            Generate boilerplate code Laravel controller, React component, atau Tailwind layout dari deskripsi teks sederhana.
                        </p>
                        <div class="flex items-center text-pink-500 font-semibold text-sm group-hover:gap-2 transition-all">
                            Buka Generator <span class="ml-1">→</span>
                        </div>
                    </a>

                    <div class="group relative bg-dark-surface rounded-2xl border border-dark-border p-6 opacity-75 hover:opacity-100 transition-all hover:border-orange-500/50">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-xl bg-dark-input flex items-center justify-center border border-dark-border text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">OCR & Image Lab</h3>
                        <p class="text-sm text-slate-500 mb-4">
                            Ekstrak teks dari foto soal atau ubah format gambar secara bulk.
                        </p>
                        <button class="w-full py-2 rounded-lg border border-dashed border-slate-600 text-slate-500 text-sm cursor-not-allowed">
                            Segera Hadir
                        </button>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-dark-surface rounded-2xl border border-dark-border p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-white">Riwayat Pengerjaan Terakhir</h3>
                            <button class="text-sm text-accent-cyan hover:underline">Lihat Semua Log</button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-slate-500 text-sm border-b border-dark-border">
                                        <th class="pb-3 font-medium">Nama File / Task</th>
                                        <th class="pb-3 font-medium">Modul</th>
                                        <th class="pb-3 font-medium">Status</th>
                                        <th class="pb-3 font-medium text-right">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-slate-300">
                                    <tr class="group border-b border-dark-border/50 hover:bg-white/5 transition-colors">
                                        <td class="py-4 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded bg-green-500/10 flex items-center justify-center text-green-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <span class="font-medium">dataset_penjualan_2024.csv</span>
                                        </td>
                                        <td class="py-4"><span class="bg-accent-cyan/10 text-accent-cyan px-2 py-0.5 rounded text-xs">Data Science</span></td>
                                        <td class="py-4">
                                            <span class="flex items-center gap-1 text-emerald-400 text-xs font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Selesai
                                            </span>
                                        </td>
                                        <td class="py-4 text-right text-slate-500 font-mono">2 min ago</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-dark-surface rounded-2xl border border-dark-border p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1">Penggunaan API</h3>
                            <p class="text-sm text-slate-500 mb-6">Limit harian token pemrosesan.</p>
                            <div class="relative w-48 h-48 mx-auto mb-6">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="96" cy="96" r="88" fill="none" stroke="#1e293b" stroke-width="12"></circle>
                                    <circle cx="96" cy="96" r="88" fill="none" stroke="#06b6d4" stroke-width="12" stroke-dasharray="552" stroke-dashoffset="138" stroke-linecap="round"></circle>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-4xl font-bold text-white">75%</span>
                                    <span class="text-xs text-slate-400">Used</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 text-center text-slate-600 text-sm">
                    <p>&copy; 2025 Tugasku Automation Labs. All systems operational.</p>
                    <p class="mt-1 font-mono text-xs">Build v2.5.1-stable • Latency: 24ms</p>
                </div>
            </div>
        </main>
    </div>
    <script>
        console.log("Tugasku System Loaded.");
    </script>
</body>
</html>