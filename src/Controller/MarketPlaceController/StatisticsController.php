<?php
namespace App\Controller;

use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin/statistiques', name: 'statistiques_')]
class StatisticsController extends AbstractController
{
    public function __construct(
        private ProduitRepository      $produitRepo,
        private EntityManagerInterface $em,
        private ChartBuilderInterface  $chartBuilder
    ) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        // Récupère les 10 produits les moins vendus
        $data = $this->produitRepo->findLeastSoldProducts(10);

        // Prépare labels & valeurs pour le graphique
        $labels = array_map(fn($row) => $row[0]->getNom(), $data);
        $values = array_map(fn($row) => $row['ventes'], $data);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels'   => $labels,
            'datasets' => [[
                'label' => 'Nombre de ventes',
                'data'  => $values,
            ]]
        ]);

        return $this->render('back/MarketPlace/Produit/statistiques_index.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/remise', name: 'remise', methods: ['POST'])]
    public function remise(): Response
    {
        // Récupère le 1er (le moins vendu)
        $result = $this->produitRepo->findLeastSoldProducts(1);
        if (empty($result)) {
            $this->addFlash('warning', 'Aucun produit à remiser.');
            return $this->redirectToRoute('statistiques_index');
        }
        [$produit, $ventes] = $result[0];

        // Applique 10% de remise
        $produit->setPrix($produit->getPrix() * 0.9);
        $this->em->flush();

        $this->addFlash('success', sprintf(
            'Remise appliquée sur « %s » (ventes : %d).',
            $produit->getNom(),
            $ventes
        ));

        return $this->redirectToRoute('statistiques_index');
    }
}
