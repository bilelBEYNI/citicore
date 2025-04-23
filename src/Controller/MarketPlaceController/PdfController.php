<?php
namespace App\Controller\MarketPlaceController;


use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/generate-pdf', name: 'generate_pdf')]
public function generate(Pdf $knpSnappyPdf): Response
{
    $html = $this->renderView('Front/MarketPlace/pdf.html.twig', [
        'client_name' => 'Rayen',
        'cart' => [
            ['name' => 'Produit A', 'quantity' => 2, 'price' => 15],
            ['name' => 'Produit B', 'quantity' => 1, 'price' => 35],
        ],
        'total' => 65
    ]);

    $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

    return new Response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="recu.pdf"',
    ]);
}

}
