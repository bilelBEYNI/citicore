<?php
namespace App\Controller;

use App\service\DescriptionGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GeminiController extends AbstractController
{
    #[Route('/api/gemini/generate-description', name: 'api_generate_description', methods: ['POST'])]
    public function generate(string $productName): ?string
{
    try {
        $response = $this->client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', [
            'query' => ['key' => $this->apiKey],
            'json' => [
                'contents' => [
                    ['parts' => [['text' => "Génère une description pour $productName"]]],
                ],
            ],
        ]);

        $data = $response->toArray();
        file_put_contents(__DIR__ . '/gemini_response.log', print_r($data, true));

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($data['candidates'][0]['content']['parts'][0]['text']);
        } else {
            file_put_contents(__DIR__ . '/gemini_error.log', 'Structure de réponse inattendue');
            return null;
        }
    } catch (\Exception $e) {
        file_put_contents(__DIR__ . '/gemini_error.log', $e->getMessage());
        return null;
    }
}
}