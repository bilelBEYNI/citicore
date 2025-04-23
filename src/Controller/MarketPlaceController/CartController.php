<?php

namespace App\Controller\MarketPlaceController;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    private SessionInterface $session;
    private EntityManagerInterface $em;

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
            if (!$produit) {
                continue;
            }
            $sousTotal = $produit->getPrix() * $qty;
            $items[] = ['produit' => $produit, 'quantity' => $qty, 'sousTotal' => $sousTotal];
            $total += $sousTotal;
        }

        return $this->render('Front/MarketPlace/cart.html.twig', [
            'items' => $items,
            'total' => $total,
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
    
        $items   = [];
        $totalHT = 0;
        $vatRate = 0.20; // 20%
    
        foreach ($panier as $id => $qty) {
            $produit = $produitRepository->find($id);
            if (!$produit) continue;
    
            $sousTotal = $produit->getPrix() * $qty;
            $items[]   = [
                'produit'  => $produit,
                'quantity' => $qty,
                'sousTotal'=> $sousTotal
            ];
            $totalHT  += $sousTotal;
        }
    
        $vatAmount = $totalHT * $vatRate;
        $totalTTC  = $totalHT + $vatAmount;
    
        return $this->render('Front/MarketPlace/confirmation.html.twig', [
            'items'     => $items,
            'totalHT'   => $totalHT,
            'vatAmount' => $vatAmount,
            'totalTTC'  => $totalTTC,
        ]);
    }
    

    #[Route('/cart/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validate(): Response
    {
        $panier = $this->session->get('panier', []);
        if (empty($panier)) {
            $this->addFlash('danger', 'Impossible de valider une commande vide.');
            return $this->redirectToRoute('app_cart');
        }

        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $this->em->persist($commande);

        foreach ($panier as $id => $qty) {
            $produit = $this->em->getRepository(Produit::class)->find($id);
            if (!$produit) {
                continue;
            }
            $cp = new CommandeProduit();
            $cp->setCommande($commande)
               ->setProduit($produit)
               ->setQuantite($qty);
            $this->em->persist($cp);
        }

        $this->em->flush();
        $this->session->remove('panier');

        $this->addFlash('success', 'Commande enregistrée avec succès !');
        return $this->redirectToRoute('app_shop');
    }

    #[Route('/cart/cancel', name: 'app_cart_cancel', methods: ['POST'])]
    public function cancel(): Response
    {
        $this->session->remove('panier');
        $this->addFlash('info', 'Commande annulée.');
        return $this->redirectToRoute('app_shop');
    }

    #[Route('/cart/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(int $id, Request $request, ProduitRepository $produitRepository): Response
    {
        $qty = (int) $request->request->get('quantity');
        $produit = $produitRepository->find($id);

        if (!$produit || $qty < 1) {
            $this->addFlash('danger', 'Produit invalide ou quantité incorrecte.');
            return $this->redirectToRoute('app_cart');
        }

        $panier = $this->session->get('panier', []);
        $panier[$id] = $qty;
        $this->session->set('panier', $panier);

        $this->addFlash('success', 'Panier mis à jour.');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/create-checkout-session', name: 'app_create_checkout_session', methods: ['POST'])]
public function createCheckoutSession(Request $request, ProduitRepository $produitRepository, UrlGeneratorInterface $router): Response
{
    // 1. Récupérer le panier
    $panier = $this->session->get('panier', []);
    if (empty($panier)) {
        $this->addFlash('warning', 'Votre panier est vide.');
        return $this->redirectToRoute('app_cart');
    }

    // 2. Configurer Stripe
    Stripe::setApiKey($this->getParameter('stripe.secret_key'));

    // 3. Construire les line items
    $lineItems = [];
    foreach ($panier as $id => $qty) {
        $produit = $produitRepository->find($id);
        if (!$produit) continue;

        $lineItems[] = [
            'price_data' => [
                'currency'     => 'eur',
                'unit_amount'  => intval($produit->getPrix() * 100),
                'product_data' => [
                    'name'        => $produit->getNom(),
                    // 'images' => [ ... ] facultatif
                ],
            ],
            'quantity' => $qty,
        ];
    }

    // 4. Créer la session Checkout
    $session = CheckoutSession::create([
        'payment_method_types' => ['card'],
        'line_items'           => $lineItems,
        'mode'                 => 'payment',
        'success_url'          => $router->generate('app_cart_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url'           => $router->generate('app_cart_cancel_payment', [], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);  
    // cf. exemple Stripe Checkout :contentReference[oaicite:1]{index=1}

    // 5. Redirection vers Stripe
    return $this->redirect($session->url, 303);
}
#[Route('/cart/success', name: 'app_cart_success')]
public function success(ProduitRepository $produitRepository): Response
{
    // Vous pouvez récupérer le session_id en GET si besoin pour vérification
    // Persistez la Commande et ses CommandeProduit ici, comme dans validate()
    // Puis :
    $this->session->remove('panier');
    $this->addFlash('success', 'Paiement effectué et commande enregistrée !');
    return $this->redirectToRoute('app_shop');
}
#[Route('/cart/cancel-payment', name: 'app_cart_cancel_payment')]
public function cancelPayment(): Response
{
    // Le panier reste intact pour que l’utilisateur puisse réessayer
    $this->addFlash('info', 'Paiement annulé, votre panier est conservé.');
    return $this->redirectToRoute('app_cart');
}
#[Route('/cart/pdf', name: 'app_cart_pdf')]
public function generatePdf(ProduitRepository $produitRepository, \Knp\Snappy\Pdf $knpSnappyPdf): Response
{
    $panier = $this->session->get('panier', []);
    $items = [];
    $totalHT = 0;
    $vatRate = 0.20;

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

    $vatAmount = $totalHT * $vatRate;
    $totalTTC = $totalHT + $vatAmount;

    $html = $this->renderView('Front/MarketPlace/pdf.html.twig', [
        'items' => $items,
        'totalHT' => $totalHT,
        'vatAmount' => $vatAmount,
        'totalTTC' => $totalTTC,
    ]);

    return new Response(
        $knpSnappyPdf->getOutputFromHtml($html),
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
    // Ne supprime rien de la session, on retourne simplement au panier
    $this->addFlash('warning', 'Commande annulée. Vous pouvez modifier votre panier.');
    return $this->redirectToRoute('app_cart'); // Remplace avec la bonne route de ton panier
}


}
