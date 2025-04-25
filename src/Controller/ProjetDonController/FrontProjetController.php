<?php

namespace App\Controller\ProjetDonController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjetDonRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Service\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class FrontProjetController extends AbstractController
{
    #[Route('/front/projet', name: 'app_front_projet')]
    public function index(Request $request, ProjetDonRepository $projetDonRepository): Response
    {
        $search = $request->query->get('search');  // Get the search query from the URL

        if ($search) {
            // If a search query is provided, filter projects by name (or other criteria)
            $projets = $projetDonRepository->findByName($search);
        } else {
            // If no search query is provided, return all projects
            $projets = $projetDonRepository->findAll();
        }

        return $this->render('Front/ProjetDon/index.html.twig', [
            'projets' => $projets,
        ]);
    }
    #[Route('/front/projet/{id}/donner', name: 'app_projet_donner')]
public function donner(Request $request, ProjetDonRepository $projetDonRepository, EntityManagerInterface $em, int $id): Response
{
    $projet = $projetDonRepository->find($id);

    if (!$projet) {
        throw $this->createNotFoundException('Projet non trouvé.');
    }

    if ($request->isMethod('POST')) {
        $montant = floatval($request->request->get('montant'));
        if ($montant > 0) {
            $projet->setMontantRecu($projet->getMontantRecu() + $montant);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre don !');
            return $this->redirectToRoute('app_projet_donner', ['id' => $id]);
        }
    }

    return $this->render('Front/ProjetDon/donner.html.twig', [
        'projet' => $projet
    ]);
}
#[Route('/front/projet/nosassociation', name: 'front_projet_nosassociation', methods: ['GET'])]
    public function nosAssociations(Request $request, AssociationRepository $associationRepository): Response
    {
        // Get the search query from the request
        $searchQuery = $request->query->get('search');

        // If there is a search query, filter the associations
        if ($searchQuery) {
            $associations = $associationRepository->findBySearch($searchQuery);
        } else {
            // Otherwise, get all associations
            $associations = $associationRepository->findAll();
        }

        return $this->render('Front/ProjetDon/nosassociation.html.twig', [
            'associations' => $associations,
        ]);
    }
    #[Route('/front/chatbot', name: 'chatbot_page')]
public function chatbot(): Response
{
    return $this->render('Front/ProjetDon/chatbot.html.twig');
}


#[Route('/front/projet/{id}/payer', name: 'app_projet_payer')]
public function payerAvecPaypal(int $id, ProjetDonRepository $projetDonRepository, PayPalClient $paypalClient, Request $request): RedirectResponse
{
    $projet = $projetDonRepository->find($id);
    if (!$projet) {
        throw $this->createNotFoundException('Projet non trouvé.');
    }

    // Get the donation amount from the form submission
    $montant = $request->request->get('montant');
    
    // Ensure montant is valid before proceeding
    if ($montant <= 0) {
        throw $this->createNotFoundException('Montant invalide.');
    }

    // Create a PayPal payment request
    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');
    $request->body = [
        "intent" => "CAPTURE",
        "purchase_units" => [[
            "amount" => [
                "currency_code" => "USD",
                "value" => number_format($montant, 2) // Use the donation amount
            ]
        ]],
        "application_context" => [
            "cancel_url" => $this->generateUrl('app_paiement_cancel', [], 0),
            "return_url" => $this->generateUrl('app_paiement_success', ['id' => $id], 0)
        ]
    ];

    $response = $paypalClient->getClient()->execute($request);

    foreach ($response->result->links as $link) {
        if ($link->rel === 'approve') {
            return new RedirectResponse($link->href);
        }
    }

    throw new \Exception('Lien d’approbation PayPal introuvable.');
}

#[Route('/paiement/success/{id}', name: 'app_paiement_success')]
public function paiementSuccess(int $id): Response
{
    // Here you can capture the payment if needed, or just show a thank you page.
    $this->addFlash('success', 'Paiement réussi via PayPal !');
    return $this->redirectToRoute('app_front_projet');
}

#[Route('/paiement/cancel', name: 'app_paiement_cancel')]
public function paiementCancel(): Response
{
    $this->addFlash('error', 'Paiement annulé.');
    return $this->redirectToRoute('app_front_projet');
}



}

