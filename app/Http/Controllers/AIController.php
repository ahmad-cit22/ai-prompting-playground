<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function handlePrompt(Request $request)
    {
        $prompt = $request->input('prompt');

        $systemPrompt = file_get_contents(resource_path('system-prompt.md'));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                ['role' => 'user', 'content' => $prompt],
            ]
        ]);

        $responseData = $response->json();

        $aiMessage = $responseData['choices'][0]['message']['content'] ?? 'No response from AI.';

        return response()->json([
            'message' => $aiMessage,
        ]);
    }
}
