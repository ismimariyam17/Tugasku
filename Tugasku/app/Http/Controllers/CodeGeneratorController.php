<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CodeGeneratorController extends Controller
{
    public function index()
    {
        return view('code_generator');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:5',
            'framework' => 'required|in:laravel,react,tailwind',
        ]);

        $prompt = $request->prompt;
        $framework = $request->framework;

        try {
            // Panggil fungsi request ke Gemini
            $generatedCode = $this->askGemini($framework, $prompt);

            return back()->with([
                'success' => 'Berhasil generate code menggunakan Gemini 2.0 Flash!',
                'generated_code' => $generatedCode,
                'prompt' => $prompt,
                'framework' => $framework
            ]);

        } catch (\Exception $e) {
            // Tampilkan error jika ada
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    private function askGemini($framework, $userPrompt)
    {
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception("API Key belum disetting.");
        }

        // 1. Susun Instruksi System
        $systemInstruction = "You are an expert software engineer specialized in $framework. "
            . "Your task is to generate clean, production-ready code based on the user description. "
            . "IMPORTANT: Output ONLY the raw code without markdown backticks (```) and without explanation text. "
            . "If comments are needed, write them inside the code.";

        // 2. Susun Prompt Spesifik
        if ($framework == 'laravel') {
            $fullPrompt = "Generate a complete Laravel Controller or Model (PHP code) for this requirement: '$userPrompt'. Include namespace App\Http\Controllers; and use Illuminate imports.";
        } elseif ($framework == 'react') {
            $fullPrompt = "Generate a functional React Component (JSX code) using Tailwind CSS for styling for: '$userPrompt'. Use 'export default function'.";
        } else {
            $fullPrompt = "Generate a pure HTML structure using Tailwind CSS classes for: '$userPrompt'. Ensure it is responsive.";
        }

        // 3. MODEL: Pakai 'gemini-2.0-flash'
        $model = 'gemini-2.0-flash';

        // 4. URL API YANG BENAR (Bersih)
        $url = "[https://generativelanguage.googleapis.com/v1beta/models/](https://generativelanguage.googleapis.com/v1beta/models/){$model}:generateContent?key={$apiKey}";

        // 5. Kirim Request
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . "\n\nRequest: " . $fullPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 4000,
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception("Gemini Error ({$response->status()}): " . $response->body());
        }

        // 6. Ambil Hasil Respons
        $responseData = $response->json();
        $rawText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '// Maaf, AI tidak memberikan output code.';

        // 7. Bersihkan Markdown
        return $this->cleanMarkdown($rawText);
    }

    private function cleanMarkdown($text)
    {
        $text = preg_replace('/^```[a-z]*\n/m', '', $text);
        $text = preg_replace('/```$/m', '', $text);
        return trim($text);
    }
}