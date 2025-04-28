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
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Service\SmsService;




final class FrontProjetController extends AbstractController
{
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
   
    #[Route('/front/projet', name: 'app_front_projet')]
    public function index(Request $request, ProjetDonRepository $projetDonRepository): Response
    {
        $search = $request->query->get('search');  // Get the search query from the URL
        $filter = $request->query->get('filter', 'all');  // Default to 'all' if no filter is selected

        if ($search) {
            // If a search query is provided, filter projects by name and availability
            if ($filter == 'available') {
                $projets = $projetDonRepository->findByNameAndAvailable($search);
            } else {
                $projets = $projetDonRepository->findByName($search);
            }
        } else {
            // If no search query is provided, handle filtering based on selected filter
            if ($filter == 'available') {
                $projets = $projetDonRepository->findAvailableProjects();
            } else {
                $projets = $projetDonRepository->findAll();
            }
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
            // Update the donation amount
            $projet->setMontantRecu($projet->getMontantRecu() + $montant);
            $em->flush();

            // Send the SMS
            $message = "Merci pour votre visite.";
            $this->smsService->sendSms('+21658764679', $message);  // Sending SMS to the correct number with country code

            $this->addFlash('success', 'Merci pour votre don !');
            // Redirect to the PayPal payment page
            return $this->redirectToRoute('app_projet_payer', ['id' => $id]);
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
public function payerAvecPaypal(
    int $id,
    ProjetDonRepository $projetDonRepository,
    PayPalClient $paypalClient,
    Request $request
): RedirectResponse {
    $projet = $projetDonRepository->find($id);
    if (!$projet) {
        throw $this->createNotFoundException('Projet non trouvé.');
    }

    $montant = $request->request->get('montant');
    if ($montant <= 0) {
        throw $this->createNotFoundException('Montant invalide.');
    }

    // hedhy teb33aaa l smssssss
    $isSmsEnabled = false; 

if ($isSmsEnabled) {
    $this->smsService->sendSms('+21658764679', 'Merci pour votre visite.');
}
    //!!!! 


    // Create a PayPal payment request
    $paypalRequest = new OrdersCreateRequest();
    $paypalRequest->prefer('return=representation');
    $paypalRequest->body = [
        "intent" => "CAPTURE",
        "purchase_units" => [[
            "amount" => [
                "currency_code" => "USD",
                "value" => (string)number_format($montant, 2, '.', '')
            ]
        ]],
        "application_context" => [
            "cancel_url" => $this->generateUrl('app_paiement_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            "return_url" => $this->generateUrl('app_paiement_success', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL)
        ]
    ];

    $response = $paypalClient->getClient()->execute($paypalRequest);

    foreach ($response->result->links as $link) {
        if ($link->rel === 'approve') {
            return new RedirectResponse($link->href);
        }
    }

    throw new \Exception('Lien d’approbation PayPal introuvable.');
}

#[Route('/paiement/success/{id}', name: 'app_paiement_success')]
public function paiementSuccess(
    int $id,
    Request $request,
    PayPalClient $paypalClient,
    ProjetDonRepository $projetDonRepository,
    EntityManagerInterface $em
): Response {
    $token = $request->query->get('token');
    if (!$token) {
        throw $this->createNotFoundException('Token manquant.');
    }

    try {
        // Capture the payment on PayPal
        $capture = new OrdersCaptureRequest($token);
        $capture->prefer('return=representation');
        $response = $paypalClient->getClient()->execute($capture);

        // Retrieve the paid amount from PayPal capture response
        $amountPaid = $response->result->purchase_units[0]->payments->captures[0]->amount->value;

        // Fetch the project
        $projet = $projetDonRepository->find($id);
        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }

        // Update the montant_recu
        $projet->setMontantRecu($projet->getMontantRecu() + (float) $amountPaid);

        // Persist changes
        $em->flush();

        $this->addFlash('success', 'Paiement réussi via PayPal ! Montant ajouté au projet.');
        return $this->redirectToRoute('app_front_projet');

    } catch (\Throwable $e) {
        $this->addFlash('error', 'Erreur lors du paiement : ' . $e->getMessage());
        return $this->redirectToRoute('app_front_projet');
    }
}

#[Route('/paiement/cancel', name: 'app_paiement_cancel')]
public function paiementCancel(): Response
{
    $this->addFlash('error', 'Paiement annulé.');
    return $this->redirectToRoute('app_front_projet');
}
#[Route('/test/sms', name: 'test_sms')]
public function testSms(): Response
{
    $this->smsService->sendSms('+21658764679', 'Test message from Symfony');
    return new Response('SMS sent');
}



}

