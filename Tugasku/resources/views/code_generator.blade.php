<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Code Architect</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'] },
                    colors: { 
                        dark: { bg: '#09090b', card: '#18181b', border: '#27272a' }, 
                        brand: { 500: '#6366f1', 600: '#4f46e5' } 
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #050505; background-image: radial-gradient(circle at 50% 0%, #1e1b4b 0%, #050505 40%); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }
        pre { margin: 0 !important; white-space: pre !important; }
        .glass { background: rgba(24, 24, 27, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="text-slate-300 min-h-screen flex flex-col" x-data="{ showHistory: false }">

    <nav class="border-b border-white/5 bg-black/20 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <h1 class="font-bold text-xl tracking-tight text-white">Code<span class="text-indigo-400">Architect</span></h1>
            </div>
            <button @click="showHistory = true" class="text-sm font-medium text-slate-400 hover:text-white transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat
            </button>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl mx-auto w-full p-6 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start relative">
        
        <div class="lg:col-span-4 lg:sticky lg:top-24 flex flex-col gap-4">
            <div class="glass rounded-2xl p-1">
                <div class="bg-dark-card/50 rounded-xl p-6">
                    <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Input Perintah
                    </h2>
                    <form action="{{ route('code_generator.generate') }}" method="POST" onsubmit="showLoading()">
                        @csrf
                        <div class="relative group">
                            <textarea 
                                name="prompt" 
                                required 
                                placeholder="Jelaskan kode apa yang ingin dibuat..." 
                                class="w-full h-64 bg-black/40 border border-white/10 rounded-xl p-4 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all resize-none leading-relaxed placeholder:text-slate-600"
                            >{{ session('prompt_used') }}</textarea>
                            <div class="absolute bottom-3 right-3 text-[10px] text-slate-600 bg-black/50 px-2 py-0.5 rounded border border-white/5">Shift+Enter utk baris baru</div>
                        </div>
                        
                        <button type="submit" class="mt-4 w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl transition-all shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-2 group">
                            <span>Generate Code</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-xs text-slate-500">Powered by Gemini AI • Unlimited Tokens</p>
            </div>
        </div>

        <div class="lg:col-span-8 min-w-0">
            <div class="glass rounded-2xl overflow-hidden shadow-2xl flex flex-col min-h-[600px]">
                <div class="bg-[#1e1e1e] border-b border-white/5 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-mono text-slate-400">
                        <div class="flex gap-1.5 mr-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500/80"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/80"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></div>
                        </div>
                        result.{{ strtolower(session('framework_used') == 'Python' ? 'py' : 'php') }}
                    </div>
                    <button onclick="copyCode()" id="copyBtn" class="text-xs bg-white/5 hover:bg-white/10 text-slate-300 px-3 py-1.5 rounded transition border border-white/5">Copy Code</button>
                </div>

                <div class="relative flex-1 bg-[#0d0d0d] group">
                    <textarea id="rawCode" class="hidden">{{ session('generated_code') }}</textarea>
                    
                    <div class="absolute inset-0 overflow-auto custom-scroll" id="codeWrapper">
                        <pre class="!bg-transparent !p-6 text-sm font-mono leading-relaxed"><code id="codeBlock" class="language-{{ strtolower(session('framework_used') == 'Python' ? 'python' : (session('framework_used') == 'C++' ? 'cpp' : 'php')) }}"></code></pre>
                    </div>

                    @if(!session('generated_code'))
                    <div id="emptyState" class="absolute inset-0 flex flex-col items-center justify-center text-slate-600">
                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        <p class="text-sm font-medium opacity-50">Menunggu perintah coding...</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <a href="{{ url('/') }}" class="fixed bottom-8 left-8 z-50 flex items-center gap-3 pl-2 pr-5 py-2.5 bg-slate-900/90 hover:bg-indigo-600 text-slate-300 hover:text-white border border-white/10 rounded-full shadow-2xl backdrop-blur-md transition-all group hover:-translate-y-1">
        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <span class="text-sm font-medium">Dashboard</span>
    </a>

    <div x-show="showHistory" style="display: none;" class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showHistory = false" x-transition.opacity></div>
        <div class="absolute right-0 top-0 h-full w-80 bg-[#121212] border-l border-white/10 shadow-2xl p-4 flex flex-col"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-white">Riwayat</h3>
                <button @click="showHistory = false" class="text-slate-500 hover:text-white">&times;</button>
            </div>
            <div class="flex-1 overflow-y-auto space-y-3">
                @forelse($history as $item)
                    <div onclick="loadHistory(`{{ js($item->prompt) }}`, `{{ js($item->code) }}`, `{{ $item->language }}`)" 
                         class="p-3 rounded-lg bg-white/5 hover:bg-indigo-500/10 hover:border-indigo-500/50 border border-transparent cursor-pointer transition group">
                        <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                            <span class="text-indigo-400 font-bold">{{ $item->language }}</span>
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-300 line-clamp-2 group-hover:text-white">{{ $item->prompt }}</p>
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-600 mt-10">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function showLoading() {
            Swal.fire({ title: '', html: 'Sedang menulis kode...', background: '#18181b', color: '#fff', showConfirmButton: false, didOpen: () => Swal.showLoading() });
        }

        // Fungsi Load History
        function loadHistory(prompt, code, lang) {
            document.querySelector('[name="prompt"]').value = prompt;
            const codeBlock = document.getElementById('codeBlock');
            codeBlock.textContent = code;
            codeBlock.className = `language-${lang.toLowerCase().includes('python') ? 'python' : 'php'}`;
            document.getElementById('emptyState').style.display = 'none';
            Prism.highlightElement(codeBlock);
            document.querySelector('.bg-black\\/80').click(); // Tutup modal
        }

        // Efek Ketik Halus
        document.addEventListener('DOMContentLoaded', () => {
            const raw = document.getElementById('rawCode').value;
            const target = document.getElementById('codeBlock');
            const isFresh = "{{ session('generated_code') ? 'yes' : 'no' }}";

            if (raw && isFresh === 'yes') {
                let i = 0;
                const speed = 2; // Makin kecil makin cepat
                function type() {
                    if (i < raw.length) {
                        target.textContent += raw.slice(i, i+5); // Tulis 5 char sekaligus biar cepat
                        i += 5;
                        document.getElementById('codeWrapper').scrollTop = document.getElementById('codeWrapper').scrollHeight;
                        requestAnimationFrame(type);
                    } else {
                        Prism.highlightElement(target);
                    }
                }
                type();
            } else if (raw) {
                target.textContent = raw;
                Prism.highlightElement(target);
            }
        });

        function copyCode() {
            navigator.clipboard.writeText(document.getElementById('codeBlock').textContent);
            const btn = document.getElementById('copyBtn');
            btn.innerText = 'Copied!';
            setTimeout(() => btn.innerText = 'Copy Code', 2000);
        }
    </script>
</body>
</html>