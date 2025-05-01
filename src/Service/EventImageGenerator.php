<?php
namespace App\Service;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine; // ou Imagine\Imagick\Imagine si tu préfères Imagick
use Symfony\Component\String\Slugger\SluggerInterface;

class EventImageGenerator
{
    private Imagine $imagine;
    private SluggerInterface $slugger;
    private string $cacheDir;

    public function __construct(SluggerInterface $slugger, string $projectDir)
    {
        $this->imagine   = new Imagine();
        $this->slugger   = $slugger;
        // dossier où on stocke l’image brute avant cache LiipImagine
        $this->cacheDir  = $projectDir . '/public/uploads/events';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Génère (ou retourne si existe déjà) une image PNG
     * contenant le titre de l’événement, sur un fond choisi.
     *
     * @return string Le chemin public vers l’image (ex: '/uploads/events/campagne-hiver-chaud.png')
     */
    public function generate(string $eventName): string
    {
        $slug = $this->slugger->slug($eventName)->lower();
        $filename = $slug . '.png';
        $publicPath = '/uploads/events/' . $filename;
        $fullPath   = $this->cacheDir . '/' . $filename;

        // si déjà générée, on retourne le même URL
        if (file_exists($fullPath)) {
            return $publicPath;
        }

        // dimensions et palette
        $width  = 1200;
        $height = 630;
        $palette = new \Imagine\Image\Palette\RGB();
        $white  = $palette->color([255, 255, 255]);
        $black  = $palette->color([0, 0, 0]);

        // choix d’un fond selon mots-clés
        $bgColors = [
            'hiver' => [50, 100, 150],
            'été'   => [230, 200, 50],
            'chaud' => [255, 120, 20],
            'campagne' => [100, 180, 80],
        ];
        // couleur par défaut
        $bg = $palette->color([200, 200, 200]);
        foreach ($bgColors as $key => $rgb) {
            if (stripos($eventName, $key) !== false) {
                $bg = $palette->color($rgb);
                break;
            }
        }

        // création de l’image
        $image = $this->imagine->create(new Box($width, $height));
        $image->draw()->rectangle(
            new \Imagine\Image\Point(0, 0),
            new \Imagine\Image\Point($width - 1, $height - 1),
            $bg,
            true
        );

        // texte
        $fontPath = __DIR__.'/../../assets/styles/fonts/arial.ttf';
        $fontSize = 48;
        $font = $this->imagine->font($fontPath, $fontSize, $black);
        // centrer le texte
        $size = $image->getSize();
        $textBox = $font->box($eventName);
        $x = ($size->getWidth()  - $textBox->getWidth())  / 2;
        $y = ($size->getHeight() - $textBox->getHeight()) / 2;

        $image->draw()->text($eventName, $font, new Point((int)$x, (int)$y));

        // sauvegarde
        $image->save($fullPath);

        return $publicPath;
    }
}