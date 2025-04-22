<?php
namespace App\service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DescriptionGenerator
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
        if (empty($this->apiKey)) {
            throw new \Exception("La clé API Gemini n'est pas définie.");
        }
    }
    public function generate(string $productName): ?string
    {
        $prompt = "Génère une description professionnelle, persuasive et concise en français pour un produit appelé \"$productName\". La description doit :
        - Être entre 50 et 100 mots
        - Mettre en valeur les bénéfices du produit
        - Utiliser un ton professionnel et commercial
        - Inclure des points forts imaginés du produit
        - Se terminer par une phrase d'accroche";

        try {
            $response = $this->client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', [
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                ],
            ]);

            $data = $response->toArray();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            // Nettoyage du texte
            if ($text) {
                $text = trim($text);
                $text = str_replace(['"', '"'], '"', $text);
                $text = preg_replace('/\s+/', ' ', $text);
            }
            
            return $text;
        } catch (\Exception $e) {
            return null;
        }
    }
}