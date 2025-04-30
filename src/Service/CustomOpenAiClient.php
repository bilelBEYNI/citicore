<?php


namespace App\Service;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class CustomOpenAiClient // Désormais gestion locale avec LiipImagineBundle
{
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    // Retourne l'URL d'une image locale traitée par LiipImagineBundle
    public function generateImageForEvent(string $eventName): ?string
    {
        // Ici, tu dois définir une logique pour associer un nom d'événement à une image locale
        // Exemple : on suppose que le nom de l'image est basé sur le nom de l'événement en slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $eventName)));
        $imagePath = '/uploads/events/' . $slug . '.jpg'; // adapte selon ton arborescence

        // Vérifie si le fichier existe réellement
        $absolutePath = __DIR__ . '/../../public' . $imagePath;
        if (!file_exists($absolutePath)) {
            return null; // Pas d'image pour cet événement
        }

        // Retourne l'URL de l'image traitée (miniature, filtre, etc.)
        return $this->cacheManager->getBrowserPath($imagePath, 'event_thumb'); // 'event_thumb' = nom du filtre LiipImagineBundle
    }
}
