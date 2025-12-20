<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\GeneratedCode; // Pastikan model ini di-import

class CodeGeneratorController extends Controller
{
    /**
     * Menampilkan halaman utama + Data Riwayat
     */
    public function index()
    {
        // [PERBAIKAN] Kita ambil data history agar View tidak error
        try {
            // Ambil 10 riwayat terakhir
            $history = GeneratedCode::latest()->take(10)->get();
        } catch (\Exception $e) {
            // Jika tabel belum dibuat, kirim array kosong agar tetap jalan
            $history = [];
        }

        // Kirim variabel $history ke view
        return view('code_generator', compact('history'));
    }

    /**
     * Memproses permintaan generate code (AI Gemini)
     */
    public function generate(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'prompt' => 'required|string|min:5|max:5000'
        ]);

        $prompt = $request->prompt;

        // 2. Deteksi Bahasa (Untuk Label UI)
        $detectedLabel = $this->detectLanguageLabel($prompt);

        // 3. Panggil Otak AI Gemini
        $generatedCode = $this->askGemini($prompt, $detectedLabel);

        // 4. Simpan ke Riwayat (Database)
        // Cek agar error API tidak disimpan
        if (!str_starts_with($generatedCode, '// ERROR') && !str_starts_with($generatedCode, '// System Error')) {
            try {
                GeneratedCode::create([
                    'user_id' => Auth::id() ?? null, // Null jika user belum login
                    'prompt' => $prompt,
                    'code' => $generatedCode,
                    'language' => $detectedLabel
                ]);
            } catch (\Exception $e) {
                // Abaikan jika gagal simpan history (misal tabel belum ada)
            }
        }

        // 5. Kembalikan ke View
        return back()->with([
            'generated_code' => $generatedCode,
            'framework_used' => $detectedLabel,
            'prompt_used' => $prompt
        ]);
    }

    // =========================================================================
    // OTAK AI (GEMINI API) - UNLIMITED TOKEN
    // =========================================================================
    private function askGemini($userPrompt, $languageLabel)
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');

        if (!$apiKey) {
            return "// ERROR: API Key belum diatur di file .env!";
        }

        $systemInstruction = <<<EOT
Anda adalah "Expert Code Generator" level senior.
Tugas Anda: Membuatkan kode program LENGKAP berdasarkan permintaan user untuk bahasa: {$languageLabel}.

ATURAN WAJIB:
1. HANYA berikan kode program saja. JANGAN ada teks pembuka/penutup.
2. JANGAN gunakan markdown block (```). Berikan raw text.
3. Pastikan kode TIDAK TERPOTONG. Tulis sampai tuntas.
EOT;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(120) // Anti RTO (2 menit)
            ->post("[https://generativelanguage.googleapis.com/v1beta/models/](https://generativelanguage.googleapis.com/v1beta/models/){$model}:generateContent?key={$apiKey}", [
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
                    'maxOutputTokens' => 8192, // Max Token
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '// Gagal mengambil respons AI.';
                
                // Bersihkan Markdown
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

    private function detectLanguageLabel($prompt)
    {
        $p = strtolower($prompt);
        if (str_contains($p, 'laravel') || str_contains($p, 'php')) return 'Laravel PHP';
        if (str_contains($p, 'python') || str_contains($p, 'py')) return 'Python';
        if (str_contains($p, 'c++') || str_contains($p, 'cpp')) return 'C++';
        return 'Auto-Detect';
    }
}