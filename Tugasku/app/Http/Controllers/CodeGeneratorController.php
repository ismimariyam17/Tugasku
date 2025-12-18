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
                'success' => 'Berhasil generate code menggunakan Gemini 2.5 Flash!',
                'generated_code' => $generatedCode,
                'prompt' => $prompt,
                'framework' => $framework
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    private function askGemini($framework, $userPrompt)
    {
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception("API Key belum disetting.");
        }

        $systemInstruction = "You are an expert software engineer specialized in $framework. "
            . "Your task is to generate clean, production-ready code based on the user description. "
            . "IMPORTANT: Output ONLY the raw code without markdown backticks (```) and without explanation text. "
            . "If comments are needed, write them inside the code.";

        // --- LOGIKA PROMPT YANG LEBIH PINTAR (FINAL) ---
        if ($framework == 'laravel') {
            // Pintar membedakan View vs Controller vs Model
            $fullPrompt = "Act as a Laravel Expert. Based on request: '$userPrompt', generate the appropriate file content. "
                . "If the user asks for a View/Page/Form, output raw Blade HTML. "
                . "If the user asks for logic, output a PHP Class (Controller/Model) with namespace.";
                
        } elseif ($framework == 'react') {
            // PERBAIKAN REACT: Agar bisa membedakan Component vs Hook/Utility
            $fullPrompt = "Act as a React Expert. Based on request: '$userPrompt', generate the appropriate code (Component, Hook, or Utility). "
                . "If creating a UI Component, use Tailwind CSS and 'export default function'. "
                . "If creating a Hook, follow standard naming conventions (use...).";
                
        } else {
            // Tailwind / HTML
            $fullPrompt = "Generate a pure HTML structure using Tailwind CSS classes for: '$userPrompt'. Ensure it is responsive.";
        }

        // --- MODEL TERBARU & GRATIS ---
        $model = 'gemini-2.5-flash';

        // --- URL BERSIH (ANTI ERROR CURL 3) ---
        $url = "[https://generativelanguage.googleapis.com/v1beta/models/](https://generativelanguage.googleapis.com/v1beta/models/){$model}:generateContent?key={$apiKey}";

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

        $responseData = $response->json();
        $rawText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '// Maaf, AI tidak memberikan output code.';

        return $this->cleanMarkdown($rawText);
    }

    private function cleanMarkdown($text)
    {
        $text = preg_replace('/^```[a-z]*\n/m', '', $text);
        $text = preg_replace('/```$/m', '', $text);
        return trim($text);
    }
}