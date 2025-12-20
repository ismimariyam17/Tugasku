<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Code Generator - Tugasku</title>
    
    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
    <script defer src="[https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js](https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js)"></script>
    <script src="[https://cdn.jsdelivr.net/npm/sweetalert2@11](https://cdn.jsdelivr.net/npm/sweetalert2@11)"></script>
    <link href="[https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css](https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css)" rel="stylesheet" />
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js](https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js)"></script>
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js](https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js)"></script>
    <link href="[https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;800&display=swap](https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;800&display=swap)" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'] },
                    colors: { dark: { base: '#0f172a', surface: '#1e293b' }, ai: { primary: '#6366f1', secondary: '#8b5cf6' } },
                    animation: { 'float': 'float 6s ease-in-out infinite' },
                    keyframes: { float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } } }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: #0f172a; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .glass-panel { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .cursor-blink::after { content: '▋'; animation: blink 1s step-end infinite; color: #6366f1; }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body class="bg-dark-base text-slate-300 font-sans min-h-screen selection:bg-ai-primary selection:text-white overflow-x-hidden" x-data="{ showHistory: false }">

    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:50px_50px] opacity-20 [mask-image:radial-gradient(circle_at_center,black_40%,transparent_100%)]"></div>
    </div>

    <div class="fixed inset-0 z-50 overflow-hidden" x-show="showHistory" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showHistory = false"></div>
        
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-dark-surface border-l border-slate-700 shadow-2xl transform transition-transform duration-300 flex flex-col"
             x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="translate-x-0" x-transition:leave-end="translate-x-full">
            
            <div class="p-6 border-b border-slate-700 flex justify-between items-center bg-dark-base/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-ai-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Generate
                </h3>
                <button @click="showHistory = false" class="text-slate-400 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                @forelse($history as $item)
                    <div class="bg-dark-base border border-slate-700 rounded-xl p-4 hover:border-ai-primary transition-colors cursor-pointer group"
                         onclick="loadHistory(`{{ js($item->prompt) }}`, `{{ js($item->code) }}`, `{{ $item->language }}`)">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-slate-800 text-ai-primary border border-slate-700">{{ $item->language }}</span>
                            <span class="text-xs text-slate-500">{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-300 line-clamp-2 font-medium group-hover:text-white">{{ $item->prompt }}</p>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500">
                        <p>Belum ada riwayat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen p-4 lg:p-8">
        
        <header class="flex flex-col items-center justify-center mb-10 mt-4 relative">
            <button @click="showHistory = true" class="absolute right-0 top-0 hidden lg:flex items-center gap-2 px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 rounded-full border border-slate-700 text-sm text-slate-400 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Riwayat</span>
            </button>

            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-800/50 border border-slate-700/50 mb-4 backdrop-blur-sm">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-mono text-emerald-400 font-bold tracking-wider">SYSTEM ONLINE</span>
            </div>
            <h1 class="text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-cyan-400 text-center tracking-tight mb-2">Neural Code Architect</h1>
            <p class="text-slate-400 text-sm max-w-xl text-center">Spesialis: Laravel PHP • C++ Console • Python Script</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 max-w-7xl mx-auto w-full flex-1">
            
            <div class="lg:col-span-4 flex flex-col gap-4 lg:sticky lg:top-8 self-start">
                <div class="glass-panel rounded-2xl p-1 shadow-2xl">
                    <div class="bg-dark-surface/50 rounded-xl p-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        <h2 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Input Prompt
                        </h2>
                        <form action="{{ route('code_generator.generate') }}" method="POST" class="flex flex-col gap-4" onsubmit="showGenerating()">
                            @csrf
                            <textarea id="promptInput" name="prompt" required placeholder="Ketik permintaan Anda disini..." class="w-full h-80 bg-dark-base/80 border border-slate-700 rounded-xl p-4 text-sm font-mono text-slate-300 focus:outline-none focus:border-indigo-500 transition-all resize-none custom-scroll">{{ session('prompt_used') }}</textarea>
                            <button type="submit" class="relative overflow-hidden w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20 group">
                                <span class="relative z-10 flex items-center justify-center gap-2"><span>Generate Code</span></span>
                                <div class="absolute top-0 -left-full w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            </button>
                        </form>
                    </div>
                </div>
                <button @click="showHistory = true" class="lg:hidden w-full py-3 bg-slate-800 rounded-xl border border-slate-700 text-slate-300 hover:text-white">Lihat Riwayat</button>
            </div>

            <div class="lg:col-span-8">
                <div class="glass-panel rounded-2xl p-1 shadow-2xl h-full min-h-[500px] flex flex-col relative overflow-hidden">
                    <div class="bg-dark-base/80 border-b border-slate-700 px-4 py-3 flex items-center justify-between backdrop-blur">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1.5"><div class="w-3 h-3 rounded-full bg-red-500/80"></div><div class="w-3 h-3 rounded-full bg-yellow-500/80"></div><div class="w-3 h-3 rounded-full bg-emerald-500/80"></div></div>
                            <div class="ml-4 flex items-center gap-2 text-xs font-mono text-slate-400">
                                <span>output.code</span> <span class="text-slate-600">/</span> <span class="text-indigo-400 font-bold" id="langLabel">{{ session('framework_used') ?? 'auto' }}</span>
                            </div>
                        </div>
                        <button onclick="copyToClipboard()" id="copyBtn" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs text-slate-300 transition-all">Copy</button>
                    </div>
                    <div class="flex-1 bg-[#0d1117] relative overflow-auto" id="codeContainer">
                        <textarea id="sourceCode" class="hidden">{{ session('generated_code') }}</textarea>
                        <pre class="!bg-transparent !m-0 !p-6 min-h-full text-sm font-mono leading-relaxed"><code id="typewriter" class="language-{{ strtolower(session('framework_used') == 'Python' ? 'python' : (session('framework_used') == 'C++' ? 'cpp' : 'php')) }} cursor-blink"></code></pre>
                        
                        @if(!session('generated_code'))
                        <div id="emptyState" class="absolute inset-0 flex flex-col items-center justify-center text-slate-600 select-none pointer-events-none">
                            <div class="w-20 h-20 mb-4 rounded-2xl bg-slate-800/50 flex items-center justify-center border border-slate-700/50"><svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg></div>
                            <p class="text-sm font-mono">Menunggu instruksi...</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed bottom-8 left-8 z-50">
        <a href="{{ url('/') }}" class="group flex items-center gap-3 pl-2 pr-5 py-3 rounded-full bg-slate-800/80 backdrop-blur-md border border-slate-700/50 shadow-2xl hover:border-indigo-500/50 hover:bg-slate-800 transition-all">
            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center group-hover:bg-indigo-600 transition-colors"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg></div>
            <span class="text-sm font-medium text-slate-300 group-hover:text-white">Dashboard</span>
        </a>
    </div>

    <script>
        function showGenerating() {
            Swal.fire({
                title: 'Sedang Berpikir...', html: 'AI sedang menulis kode panjang...<br>Mohon tunggu sebentar.', timerProgressBar: true, background: '#1e293b', color: '#fff', showConfirmButton: false, allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        // [BARU] Fungsi Load History
        function loadHistory(prompt, code, lang) {
            // Isi prompt
            document.getElementById('promptInput').value = prompt;
            // Isi hidden textarea source code
            const sourceEl = document.getElementById('sourceCode');
            sourceEl.value = code;
            sourceEl.classList.remove('hidden'); // Optional: reset state

            // Update Label Bahasa
            document.getElementById('langLabel').innerText = lang;

            // Reset Tampilan Code Block
            const codeBlock = document.getElementById('typewriter');
            codeBlock.textContent = code; // Langsung isi tanpa efek ketik agar cepat
            codeBlock.className = `language-${lang.toLowerCase().includes('python') ? 'python' : (lang.toLowerCase().includes('c++') ? 'cpp' : 'php')}`;
            
            // Sembunyikan Empty State
            const emptyState = document.getElementById('emptyState');
            if(emptyState) emptyState.style.display = 'none';

            // Highlight ulang
            Prism.highlightElement(codeBlock);

            // Tutup sidebar (menggunakan AlpineJS dispatch event atau direct click trigger)
            // Cara hacky tapi mudah: click backdrop
            document.querySelector('.bg-black\\/60').click(); 
        }

        // Auto Type Effect (Hanya jalan jika baru generate, bukan load history)
        document.addEventListener("DOMContentLoaded", function() {
            const source = document.getElementById('sourceCode');
            const target = document.getElementById('typewriter');
            // Cek apakah ini hasil redirect generate (session ada)
            const isFreshGenerate = "{{ session('generated_code') ? 'yes' : 'no' }}";

            if (source && target && source.value && isFreshGenerate === 'yes') {
                const text = source.value;
                let i = 0;
                function typeWriter() {
                    if (i < text.length) {
                        target.textContent += text.charAt(i);
                        const container = document.getElementById('codeContainer');
                        if(container) container.scrollTop = container.scrollHeight;
                        if (i % 100 === 0) Prism.highlightElement(target);
                        i++;
                        setTimeout(typeWriter, 1);
                    } else {
                        target.classList.remove('cursor-blink');
                        Prism.highlightElement(target);
                    }
                }
                typeWriter();
            }
        });

        function copyToClipboard() {
            const code = document.getElementById('typewriter').textContent || document.getElementById('sourceCode').value;
            if(!code) return;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.innerText = 'Disalin!';
                btn.classList.add('bg-emerald-600', 'text-white');
                setTimeout(() => {
                    btn.innerText = 'Copy';
                    btn.classList.remove('bg-emerald-600', 'text-white');
                }, 2000);
            });
        }
        
        @if($errors->any())
            Swal.fire({ icon: 'error', title: 'Oops...', text: '{{ $errors->first() }}', background: '#1e293b', color: '#fff' });
        @endif
    </script>
</body>
</html>