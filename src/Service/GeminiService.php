<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class GeminiService
{
   

    public function generateProductDescription(string $nomProduit): ?string
    {
        $prompt = sprintf('Rédige une description marketing concise et attrayante pour un produit nommé "%s".', $nomProduit);

        try {
            $response = $this->httpClient->request(
                'POST',
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent' . $this->apiKey,
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' => [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $prompt]],
                            ],
                        ],
                    ],
                ]
            );

            $data = $response->toArray();

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } catch (\Throwable $e) {
            $this->logger?->error('Erreur Gemini: ' . $e->getMessage());
            return null;
        }
    }
}
