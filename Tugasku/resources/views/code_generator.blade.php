<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Code Generator - Tugasku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: { bg: '#0B1120', surface: '#151F32', border: '#1E293B', input: '#0F172A' },
                        accent: { pink: '#ec4899', purple: '#8b5cf6', red: '#ef4444', green: '#10b981' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <style>
        .glass { background: rgba(21, 31, 50, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0B1120; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased min-h-screen flex flex-col">

    <header class="h-16 glass flex items-center justify-between px-6 sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="p-2 rounded-lg hover:bg-dark-input transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="font-bold text-white text-lg flex items-center gap-2">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-500">AI Code Generator</span>
            </h1>
        </div>
    </header>

    <main class="flex-1 p-6 max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="space-y-6">
            
            @if($errors->any())
            <div class="bg-accent-red/10 border border-accent-red/20 text-accent-red px-4 py-3 rounded-xl">
                <strong class="font-bold">Terjadi Kesalahan!</strong>
                <ul class="list-disc list-inside text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div class="bg-accent-green/10 border border-accent-green/20 text-accent-green px-4 py-3 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-dark-surface border border-dark-border rounded-2xl p-6 shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4">Parameter Generator</h2>
                <form action="{{ route('code_generator.generate') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Pilih Framework / Bahasa</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="framework" value="laravel" class="peer hidden" {{ (old('framework') == 'laravel' || session('framework') == 'laravel' || !session('framework')) ? 'checked' : '' }}>
                                <div class="p-3 rounded-xl bg-dark-input border border-dark-border peer-checked:border-accent-pink peer-checked:bg-accent-pink/10 hover:border-slate-500 transition-all text-center">
                                    <span class="font-bold text-sm block text-white">Laravel</span>
                                    <span class="text-xs text-slate-500">PHP</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="framework" value="react" class="peer hidden" {{ (old('framework') == 'react' || session('framework') == 'react') ? 'checked' : '' }}>
                                <div class="p-3 rounded-xl bg-dark-input border border-dark-border peer-checked:border-accent-pink peer-checked:bg-accent-pink/10 hover:border-slate-500 transition-all text-center">
                                    <span class="font-bold text-sm block text-white">React</span>
                                    <span class="text-xs text-slate-500">JSX</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="framework" value="tailwind" class="peer hidden" {{ (old('framework') == 'tailwind' || session('framework') == 'tailwind') ? 'checked' : '' }}>
                                <div class="p-3 rounded-xl bg-dark-input border border-dark-border peer-checked:border-accent-pink peer-checked:bg-accent-pink/10 hover:border-slate-500 transition-all text-center">
                                    <span class="font-bold text-sm block text-white">Tailwind</span>
                                    <span class="text-xs text-slate-500">HTML</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Deskripsi Fitur</label>
                        <textarea name="prompt" rows="6" class="w-full bg-dark-input border border-dark-border rounded-xl p-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-accent-pink focus:border-transparent focus:outline-none resize-none" placeholder="Contoh: Buatkan controller CRUD untuk data mahasiswa..." required>{{ old('prompt', session('prompt')) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-accent-pink to-accent-purple text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 hover:scale-[1.02] transition-transform">
                        Generate Code ⚡
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col h-full">
            <div class="bg-dark-surface border border-dark-border rounded-2xl flex-1 flex flex-col overflow-hidden shadow-2xl relative">
                
                <div class="bg-dark-input border-b border-dark-border p-3 flex justify-between items-center">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <span class="text-xs text-slate-500 font-mono">generated_result.php</span>
                    <button onclick="copyCode()" class="text-xs text-accent-pink hover:text-white flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        Copy
                    </button>
                </div>

                <div class="flex-1 bg-[#282c34] overflow-auto custom-scrollbar relative group">
                    @if(session('generated_code'))
                        <pre class="m-0 p-4"><code id="codeBlock" class="language-{{ session('framework') == 'laravel' ? 'php' : (session('framework') == 'react' ? 'javascript' : 'html') }}">{{ session('generated_code') }}</code></pre>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-600 opacity-50">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <p class="text-sm">Kode hasil generate akan muncul di sini...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </main>

    <script>
        function copyCode() {
            const code = document.getElementById('codeBlock').innerText;
            navigator.clipboard.writeText(code).then(() => {
                alert('Kode berhasil disalin ke clipboard!');
            });
        }
    </script>
</body>
</html>