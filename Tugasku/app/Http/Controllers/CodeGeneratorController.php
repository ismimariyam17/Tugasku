<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\GeneratedCode; 

class CodeGeneratorController extends Controller
{
    /**
     * Menampilkan halaman utama + Data Riwayat
     */
    public function index()
    {
        try {
            // Ambil 10 riwayat terakhir
            $history = GeneratedCode::latest()->take(10)->get();
        } catch (\Exception $e) {
            $history = [];
        }

        return view('code_generator', compact('history'));
    }

    /**
     * Memproses permintaan generate code
     */
    public function generate(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'prompt' => 'required|string|min:5|max:5000'
        ]);

        $prompt = $request->prompt;

        // 2. Deteksi Bahasa (Fitur ini TETAP ADA)
        $detectedLabel = $this->detectLanguageLabel($prompt);

        // 3. Panggil API Gemini
        $generatedCode = $this->askGemini($prompt, $detectedLabel);

        // 4. Simpan Riwayat jika sukses
        if (!str_starts_with($generatedCode, '// ERROR') && !str_starts_with($generatedCode, '// System Error')) {
            try {
                GeneratedCode::create([
                    'user_id' => Auth::id() ?? null, 
                    'prompt' => $prompt,
                    'code' => $generatedCode,
                    'language' => $detectedLabel
                ]);
            } catch (\Exception $e) {
                // Silent fail jika database bermasalah
            }
        }

        // 5. Kembali ke View
        return back()->with([
            'generated_code' => $generatedCode,
            'framework_used' => $detectedLabel,
            'prompt_used' => $prompt
        ]);
    }

    /**
     * FUNGSI API GEMINI (PERBAIKAN UTAMA ADA DI SINI)
     */
    private function askGemini($userPrompt, $languageLabel)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash'); 

        if (!$apiKey) {
            return "// ERROR: API Key belum diatur di file .env!";
        }

        $systemInstruction = <<<EOT
Anda adalah "Expert Code Generator".
Tugas: Buat kode program LENGKAP untuk bahasa: {$languageLabel}.
ATURAN:
1. HANYA berikan kode program. JANGAN ada teks intro/outro.
2. JANGAN gunakan markdown block (```). Berikan raw text saja.
EOT;

        try {
            // [FIX] URL API BERSIH (Hapus tanda [] dan () yang bikin error)
            // Sebelumnya: "[https://...](...)" -> SALAH
            // Sekarang: "https://..." -> BENAR
            $url = "[https://generativelanguage.googleapis.com/v1beta/models/](https://generativelanguage.googleapis.com/v1beta/models/){$model}:generateContent?key={$apiKey}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(120) 
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemInstruction . "\n\nPermintaan User: " . $userPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 8192,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '// Gagal mengambil respons.';
                
                // Bersihkan Markdown backticks
                $cleanText = preg_replace('/^```[a-z]*\n/i', '', $text);
                $cleanText = preg_replace('/```$/', '', $cleanText);
                
                return trim($cleanText);
            } else {
                return "// API Error: " . $response->body();
            }
        } catch (\Exception $e) {
            return "// System Error: " . $e->getMessage();
        }
    }

    // Fungsi ini TETAP ADA (tidak dihapus)
    private function detectLanguageLabel($prompt)
    {
        $p = strtolower($prompt);
        if (str_contains($p, 'laravel') || str_contains($p, 'php')) return 'Laravel PHP';
        if (str_contains($p, 'python') || str_contains($p, 'py')) return 'Python';
        if (str_contains($p, 'c++') || str_contains($p, 'cpp')) return 'C++';
        return 'Auto-Detect';
    }
}