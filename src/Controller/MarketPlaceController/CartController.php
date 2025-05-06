<?php
namespace App\Controller\MarketPlaceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Symfony\Component\HttpFoundation\Request;
use Knp\Snappy\Pdf;
class CartController extends AbstractController
{
    private SessionInterface $session;
    private EntityManagerInterface $em;
    private const VAT_RATE = 0.20;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->session = $requestStack->getSession();
        $this->em = $em;
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(ProduitRepository $produitRepository): Response
    {
        $panier = $this->session->get('panier', []);
        $items = [];
        $total = 0;

        foreach ($panier as $id => $qty) {
            $produit = $produitRepository->find($id);
            if (!$produit) continue;
            $sousTotal = $produit->getPrix() * $qty;
            $items[] = ['produit' => $produit, 'quantity' => $qty, 'sousTotal' => $sousTotal];
            $total += $sousTotal;
        }

        return $this->render('Front/MarketPlace/cart.html.twig', [
            'items' => $items,
            'total' => $total,
            'cartCount' => array_sum($panier),
        ]);
    }

    #[Route('/cart/confirmation', name: 'app_cart_confirmation', methods: ['GET'])]
    public function confirmation(ProduitRepository $produitRepository): Response
    {
        $panier = $this->session->get('panier', []);
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $items = [];
        $totalHT = 0;

        foreach ($panier as $id => $qty) {
            $produit = $produitRepository->find($id);
            if (!$produit) continue;
            $sousTotal = $produit->getPrix() * $qty;
            $items[] = ['produit' => $produit, 'quantity' => $qty, 'sousTotal' => $sousTotal];
            $totalHT += $sousTotal;
        }

        $vatAmount = $totalHT * self::VAT_RATE;
        $totalTTC = $totalHT + $vatAmount;

        return $this->render('Front/MarketPlace/confirmation.html.twig', [
            'items' => $items,
            'totalHT' => $totalHT,
            'vatAmount' => $vatAmount,
            'totalTTC' => $totalTTC,
            'cartCount' => array_sum($panier),
        ]);
    }

    #[Route('/cart/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validate(): Response
    {
        $panier = $this->session->get('panier', []);
        if (empty($panier)) {
            return $this->redirectToRoute('app_cart');
        }

        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $this->em->persist($commande);

        foreach ($panier as $id => $qty) {
            $produit = $this->em->getRepository(Produit::class)->find($id);
            if (!$produit) continue;

            $cp = new CommandeProduit();
            $cp->setCommande($commande)
                ->setProduit($produit)
                ->setQuantite($qty);

            $this->em->persist($cp);
        }

        $this->em->flush();
        $this->session->remove('panier');

        return $this->redirectToRoute('app_shop');
    }

    #[Route('/cart/cancel', name: 'app_cart_cancel', methods: ['POST'])]
    public function cancel(): Response
    {
        $this->session->remove('panier');
        return $this->redirectToRoute('app_shop');
    }
    #[Route('/cart/update/{id}', name: 'app_cart_update', methods: ['POST'])]
public function update(int $id, Request $request, SessionInterface $session, ProduitRepository $produitRepo): \Symfony\Component\HttpFoundation\RedirectResponse
{
    $panier = $session->get('panier', []);
    $operation = $request->request->get('operation');

    if (!isset($panier[$id])) {
        return $this->redirectToRoute('app_cart');
    }

    if ($operation === 'plus') {
        $panier[$id]++;
    } elseif ($operation === 'minus') {
        $panier[$id]--;
        if ($panier[$id] <= 0) {
            unset($panier[$id]);
        }
    }

    $session->set('panier', $panier);

    return $this->redirectToRoute('app_cart');
}
/*
     #[Route('/cart/create-checkout-session', name: 'app_create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request, ProduitRepository $produitRepository, UrlGeneratorInterface $router): Response
    {
        $panier = $this->session->get('panier', []);
        if (empty($panier)) {
            return $this->redirectToRoute('app_cart');
        }

        Stripe::setApiKey($this->getParameter('stripe.secret_key'));

        $lineItems = [];
        foreach ($panier as $id => $qty) {
            $produit = $produitRepository->find($id);
            if (!$produit) continue;

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => intval($produit->getPrix() * 100),
                    'product_data' => [
                        'name' => $produit->getNom(),
                    ],
                ],
                'quantity' => $qty,
            ];
        }

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $router->generate('app_cart_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $router->generate('app_cart_cancel_payment', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/cart/success', name: 'app_cart_success')]
    public function success(): Response
    {
        $this->session->remove('panier');
        return $this->redirectToRoute('app_shop');
    }
*/
    #[Route('/cart/cancel-payment', name: 'app_cart_cancel_payment')]
    public function cancelPayment(): Response
    {
        return $this->redirectToRoute('app_cart');
    }


#[Route('/webhook/stripe', name: 'app_stripe_webhook', methods: ['POST'])]
public function stripeWebhook(Request $request): Response
{
    // Récupérer le contenu du webhook Stripe
    $payload = $request->getContent();
    $sigHeader = $request->headers->get('Stripe-Signature');
    $endpointSecret = $this->getParameter('stripe.endpoint_secret');  // Votre clé secrète du webhook Stripe

    // Vérifier la signature du webhook
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $endpointSecret
        );

        // Traiter l'événement uniquement si c'est une session de paiement réussie
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;  // L'objet contenant la session de paiement

            // Récupérer l'ID de la commande depuis les métadonnées
            $commandeId = $session->metadata->commande_id;
            $commande = $this->em->getRepository(Commande::class)->find($commandeId);

            if ($commande) {
                // Marquer la commande comme payée et enregistrer la commande
                $commande->setStatus('paid');  // Assurez-vous que l'entité Commande a un champ "status"
                $commande->setStripeSessionId($session->id);  // Enregistrer l'ID de session Stripe pour référence
                $this->em->flush();

                // Vous pouvez également notifier l'utilisateur par email, ou mettre à jour d'autres champs si nécessaire
                // Exemple : envoyer un email de confirmation ou effectuer d'autres actions.
            }
        }

        return new Response('Webhook handled', 200);
    } catch (\Exception $e) {
        // Si une erreur survient, retourner un code 400
        return new Response('Webhook error: ' . $e->getMessage(), 400);
    }
}

#[Route('/cart/create-checkout-session', name: 'app_create_checkout_session', methods: ['POST'])]
public function createCheckoutSession(Request $request, ProduitRepository $produitRepository, UrlGeneratorInterface $router): Response
{
    $panier = $this->session->get('panier', []);
    if (empty($panier)) {
        return $this->redirectToRoute('app_cart');
    }

    Stripe::setApiKey($this->getParameter('stripe.secret_key'));

    $lineItems = [];
    foreach ($panier as $id => $qty) {
        $produit = $produitRepository->find($id);
        if (!$produit) continue;

        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => intval($produit->getPrix() * 100),
                'product_data' => [
                    'name' => $produit->getNom(),
                ],
            ],
            'quantity' => $qty,
        ];
    }

    // Créer une commande dans la base de données avant de rediriger vers Stripe
    $commande = new Commande();
    $commande->setDateCommande(new \DateTime());
    $this->em->persist($commande);
    $this->em->flush();

    // Ajouter l'ID de la commande dans les métadonnées Stripe pour le retrouver plus tard
    $session = CheckoutSession::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $router->generate('app_cart_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $router->generate('app_cart_cancel_payment', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'metadata' => [
            'commande_id' => $commande->getId(),  // Ajouter l'ID de la commande
        ],
    ]);

    return $this->redirect($session->url, 303);
}
#[Route('/cart/success', name: 'app_cart_success')]
public function success(): Response
{
    // Optionnel : vous pouvez envoyer un email de confirmation ici.
    $this->session->remove('panier');  // Vider le panier

    return $this->redirectToRoute('app_shop');  // Rediriger vers la page de la boutique
}

#[Route('/cart/pdf', name: 'app_cart_pdf', methods: ['GET'])]
public function generatePdf(ProduitRepository $produitRepository): Response
{
    $panier = $this->session->get('panier', []);
    if (empty($panier)) {
        $this->addFlash('warning', 'Votre panier est vide.');
        return $this->redirectToRoute('app_cart');
    }

    $items = [];
    $totalHT = 0;

    foreach ($panier as $id => $qty) {
        $produit = $produitRepository->find($id);
        if (!$produit) continue;
        $sousTotal = $produit->getPrix() * $qty;
        $items[] = [
            'produit' => $produit,
            'quantity' => $qty,
            'sousTotal' => $sousTotal
        ];
        $totalHT += $sousTotal;
    }

    $vatAmount = $totalHT * self::VAT_RATE;
    $totalTTC = $totalHT + $vatAmount;

    // Configuration PDF
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->setIsRemoteEnabled(true); // pour permettre les images embarquées

    $dompdf = new Dompdf($pdfOptions);

    // Encodage du logo en base64
    $logoPath = realpath($this->getParameter('kernel.project_dir') . '/public/styles/img/ty.jpg');
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/jpeg;base64,' . $logoBase64;

    // Rendu HTML avec Twig
    $html = $this->renderView('Front/MarketPlace/pdf.html.twig', [
        'items' => $items,
        'totalHT' => $totalHT,
        'vatAmount' => $vatAmount,
        'totalTTC' => $totalTTC,
        'logoSrc' => $logoSrc
    ]);

    // Génération du PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Réponse PDF
    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="commande.pdf"',
        ]
    );
}
    #[Route('/commande/annuler', name: 'annuler_commande', methods: ['POST'])]
    public function annulerCommande(): Response
    {
        return $this->redirectToRoute('app_cart');
    } 
  
}
