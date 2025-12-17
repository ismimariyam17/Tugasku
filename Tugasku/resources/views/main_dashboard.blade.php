<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white shadow-sm border-b border-slate-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="font-bold text-xl text-slate-800 tracking-tight">Portal<span class="text-blue-600">Tugasku</span></div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500">Halo, Mahasiswa</span>
               
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-8">
        
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Selamat Datang di Ruang Belajar</h1>
            <p class="text-slate-500 text-lg">Silakan pilih mata kuliah yang ingin kamu kerjakan hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <a href="{{ route('data_science.index') }}" class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 bg-blue-100 text-blue-600 text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-xl">AKTIF</div>
                <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2 group-hover:text-blue-600 transition-colors">Data Science</h3>
                <p class="text-sm text-slate-500 mb-4">Pengolahan data, visualisasi, prediksi regresi, clustering, dan klasifikasi data.</p>
                <span class="text-blue-600 font-semibold text-sm flex items-center gap-1">
                    Buka Kelas <span class="group-hover:translate-x-1 transition-transform">→</span>
                </span>
            </a>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 opacity-60 grayscale cursor-not-allowed">
                <div class="w-14 h-14 bg-purple-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Pemrograman Web</h3>
                <p class="text-sm text-slate-500 mb-4">Pengembangan aplikasi web menggunakan Laravel dan React.</p>
                <span class="text-slate-400 text-xs font-bold border border-slate-200 px-2 py-1 rounded">SEGERA HADIR</span>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 opacity-60 grayscale cursor-not-allowed">
                <div class="w-14 h-14 bg-orange-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Mobile Dev</h3>
                <p class="text-sm text-slate-500 mb-4">Pembuatan aplikasi Android & iOS dengan Flutter.</p>
                <span class="text-slate-400 text-xs font-bold border border-slate-200 px-2 py-1 rounded">SEGERA HADIR</span>
            </div>

        </div>
    </div>

</body>
</html>