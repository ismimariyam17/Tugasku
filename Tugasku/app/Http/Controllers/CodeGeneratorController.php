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
            $generatedCode = $this->askGemini($framework, $prompt);

            return back()->with([
                'success' => 'Berhasil generate code menggunakan Gemini!',
                'generated_code' => $generatedCode,
                'prompt' => $prompt,
                'framework' => $framework
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    private function askGemini(string $framework, string $userPrompt): string
    {
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception("GEMINI_API_KEY belum disetting di .env");
        }

        $systemInstruction =
            "You are an expert software engineer specialized in {$framework}. " .
            "Your task is to generate clean, production-ready code based on the user description. " .
            "IMPORTANT: Output ONLY the raw code without markdown backticks (```) and without explanation text. " .
            "If comments are needed, write them inside the code.";

        if ($framework === 'laravel') {
            $fullPrompt =
                "Generate a complete Laravel Controller or Model (PHP code) for this requirement: '{$userPrompt}'. " .
                "Include namespace App\\Http\\Controllers; and use Illuminate imports.";
        } elseif ($framework === 'react') {
            $fullPrompt =
                "Generate a functional React Component (JSX code) using Tailwind CSS for styling for: '{$userPrompt}'. " .
                "Use 'export default function'.";
        } else { // tailwind
            $fullPrompt =
                "Generate a pure HTML structure using Tailwind CSS classes for: '{$userPrompt}'. " .
                "Ensure it is responsive.";
        }

        // Bisa diganti di .env: GEMINI_MODEL=gemini-1.5-flash / gemini-2.0-flash
     $model = 'gemini-2.5-flash';

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
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
                ],
            ]);

        if ($response->failed()) {
            throw new \Exception("Gemini Error ({$response->status()}): " . $response->body());
        }

        $data = $response->json();
        $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (trim($rawText) === '') {
            $rawText = '// Maaf, AI tidak memberikan output code.';
        }

        return $this->cleanMarkdown($rawText);
    }

    private function cleanMarkdown(string $text): string
    {
        $text = preg_replace('/^```[a-z]*\s*/mi', '', $text);
        $text = preg_replace('/\s*```$/m', '', $text);
        return trim($text);
    }
}
