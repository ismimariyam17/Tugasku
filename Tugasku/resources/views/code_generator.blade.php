<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Code Generator - Tugasku</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: { bg: '#0B1120', surface: '#151F32', border: '#1E293B', input: '#0F172A' },
                        accent: { indigo: '#6366f1', purple: '#8b5cf6', cyan: '#06b6d4' }
                    },
                    fontFamily: {
                        mono: ['"Fira Code"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace'],
                    },
                    keyframes: {
                        blink: { '0%, 100%': { opacity: 1 }, '50%': { opacity: 0 } }
                    },
                    animation: {
                        blink: 'blink 1s step-end infinite'
                    }
                }
            }
        }
    </script>
    <style>
        .cursor::after {
            content: '▋';
            display: inline-block;
            vertical-align: bottom;
            animation: blink 1s step-end infinite;
            color: #6366f1;
            margin-left: 2px;
        }
        .bg-grid-pattern {
            background-image: linear-gradient(to right, #1e293b 1px, transparent 1px),
                              linear-gradient(to bottom, #1e293b 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: linear-gradient(to bottom, transparent, black, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent 10%, black 40%, black 70%, transparent 90%);
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-dark-bg text-slate-300 font-sans antialiased overflow-hidden selection:bg-accent-indigo selection:text-white">

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

        <main class="flex-1 flex flex-col items-center justify-center p-6 relative z-10 w-full max-w-6xl mx-auto">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 mb-4 shadow-[0_0_30px_-5px_rgba(16,185,129,0.3)]">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-2 tracking-tight">AI Code Generator</h1>
                <p class="text-slate-400">Cukup ketik apa yang Anda butuhkan, AI akan mendeteksi bahasa & menulisnya.</p>
            </div>

            <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-6 h-[600px]">
                
                <div class="lg:col-span-1 bg-dark-surface border border-dark-border rounded-3xl p-6 flex flex-col shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-10 opacity-10 pointer-events-none">
                        <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Input Prompt
                    </h3>
                    
                    <form action="{{ route('code_generator.generate') }}" method="POST" class="flex-1 flex flex-col gap-4" onsubmit="showGenerating()">
                        @csrf
                        
                        <div class="flex-1 flex flex-col relative z-10">
                            <label class="block text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider">Deskripsi Kebutuhan</label>
                            <textarea name="prompt" required placeholder="Contoh: &#10;- Buatkan script python untuk data science &#10;- Form login HTML keren &#10;- Kalkulator React JS" 
                                class="flex-1 w-full bg-dark-input border border-dark-border rounded-xl p-4 text-sm text-white focus:outline-none focus:border-emerald-500 transition-colors resize-none placeholder-slate-600 leading-relaxed">{{ session('prompt_used') }}</textarea>
                        </div>
                        
                        <div class="mt-2 relative z-10">
                            <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-900/30 flex items-center justify-center gap-2 group">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>Generate Code</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-[#1e1e1e] border border-dark-border rounded-3xl overflow-hidden shadow-2xl flex flex-col relative group">
                    <div class="bg-[#2d2d2d] px-4 py-3 flex items-center justify-between border-b border-[#3e3e3e]">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            
                            @if(session('framework_used'))
                                <span class="ml-3 px-2 py-0.5 rounded bg-gray-700 text-[10px] text-emerald-400 font-bold uppercase tracking-wide border border-gray-600">
                                    {{ session('framework_used') }} Detected
                                </span>
                            @else
                                <span class="ml-2 text-xs text-slate-400 font-mono">output.code</span>
                            @endif
                        </div>
                        
                        <button onclick="copyToClipboard()" id="copyBtn" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span>Copy</span>
                        </button>
                    </div>

                    <div class="flex-1 overflow-auto p-4 relative" id="codeContainer">
                        @if(session('generated_code'))
                            <pre class="language-{{ strtolower(session('framework_used') == 'C++' ? 'cpp' : (session('framework_used') == 'HTML/CSS' ? 'html' : session('framework_used'))) }} bg-transparent !p-0 !m-0"><code id="typewriter" class="text-sm font-mono leading-relaxed cursor"></code></pre>
                            <textarea id="sourceCode" class="hidden">{{ session('generated_code') }}</textarea>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-slate-600">
                                <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                <p class="text-sm font-mono">Menunggu input...</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>
        function showGenerating() {
            Swal.fire({
                title: 'Menganalisis Prompt...',
                html: 'AI sedang mendeteksi bahasa & menulis kode.<br><b class="text-emerald-400 text-lg mt-2 inline-block">Generating Logic...</b>',
                timerProgressBar: true,
                background: '#151F32',
                color: '#fff',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            const source = document.getElementById('sourceCode');
            const target = document.getElementById('typewriter');
            
            if (source && target) {
                const text = source.value;
                let i = 0;
                const speed = 5; 

                function typeWriter() {
                    if (i < text.length) {
                        target.textContent += text.charAt(i);
                        const container = document.getElementById('codeContainer');
                        container.scrollTop = container.scrollHeight;

                        if (i % 10 === 0 || i === text.length - 1) {
                            Prism.highlightElement(target);
                        }

                        i++;
                        setTimeout(typeWriter, speed);
                    } else {
                        target.classList.remove('cursor');
                    }
                }
                typeWriter();
            }
        });

        function copyToClipboard() {
            const code = document.getElementById('sourceCode').value;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.innerHTML = `<span class="text-emerald-400">Copied!</span>`;
                setTimeout(() => {
                    btn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Copy</span>`;
                }, 2000);
            });
        }

        @if($errors->any())
             Swal.fire({
                title: 'Error!',
                text: '{{ $errors->first() }}',
                icon: 'error',
                background: '#151F32',
                color: '#fff',
                confirmButtonColor: '#f43f5e'
            });
        @endif
    </script>
</body>
</html>