<?php


namespace App\Controller\UtilisateurController;

use App\Form\LoginFormType;
use App\Form\RegistrationType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class LoginController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private string $recaptchaSecret;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->recaptchaSecret = $_ENV['GOOGLE_RECAPTCHA_SECRET'];
    }
    #[Route('/login', name: 'login')]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // 1) Si déjà connecté → redirection
        if ($this->getUser()) {
            return $this->redirectBasedOnRole($this->getUser());
        }

        // 2) ReCAPTCHA
        if ($request->isMethod('POST')) {
            $recaptchaResponse = $request->request->get('g-recaptcha-response', '');
            if (empty($recaptchaResponse) || !$this->verifyRecaptcha($recaptchaResponse)) {
                $this->addFlash('error', 'Veuillez valider le reCAPTCHA.');
                return $this->redirectToRoute('login');
            }
        }

        // 3) Créer et traiter le formulaire
        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        // 4) Si soumis et valide → tentative d’authentification
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // 4.a) Cherche l'utilisateur par CIN
            /** @var Utilisateur|null $user */
            $user = $em
                ->getRepository(Utilisateur::class)
                ->findOneBy(['cin' => $data['cin']]);

            if (!$user) {
                $form->addError(new FormError('CIN ou mot de passe invalide.'));
            } else {
                // 4.b) Vérifie le mot de passe
                if ($passwordHasher->isPasswordValid($user, $data['password'])) {
                    // 5) Authentifie la session
                    $token = new UsernamePasswordToken(
                        $user,
                        'main',          // fire­wall name (security.yaml)
                        $user->getRoles()
                    );
                    $this->container
                         ->get('security.token_storage')
                         ->setToken($token);
                    $request->getSession()
                         ->set('_security_main', serialize($token));

                    // 6) Redirection selon rôle
                    return $this->redirectBasedOnRole($user);
                } else {
                    $form->addError(new FormError('CIN ou mot de passe invalide.'));
                }
            }
        }

        // Affichage du form (GET ou échec)
        return $this->render('security/login.html.twig', [
            'form'                     => $form->createView(),
            'google_recaptcha_site_key'=> $_ENV['GOOGLE_RECAPTCHA_SITE_KEY'],
        ]);
    }

    private function verifyRecaptcha(string $captchaResponse): bool
    {
        // Effectuer la vérification du reCAPTCHA via l'API de Google
        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->recaptchaSecret,
                'response' => $captchaResponse,
            ],
        ]);
        $data = $response->toArray();

        return isset($data['success']) && $data['success'] === true;
    }

    private function redirectBasedOnRole($user): Response
    {
        $roles = $user->getRoles();

        
        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->redirectToRoute('admin_dashboard');
        }

        if (in_array('ROLE_PARTICIPANT', $roles, true)) {
            return $this->redirectToRoute('participant_dashboard');
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('back/dashboard.html.twig', [
            'controller_name' => 'Admin Dashboard',
        ]);
    }

    #[Route('/participant/dashboard', name: 'participant_dashboard')]
    public function participantDashboard(UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PARTICIPANT');

        $organisateurs = $utilisateurRepository->findBy(['Role' => 'Organisateur']);

        return $this->render('front/utilisateur/participant.html.twig', [
            'organisateurs' => $organisateurs,
        ]);
    }

   

    #[Route('/SignUp', name: 'SignUp')]
    public function signup(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        

            // **Récupérer et hasher le mot de passe « plainPassword »**
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            // **Rôle par défaut**
            $user->setRole('PARTICIPANT');

            // Persistance
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('login');
        }

        return $this->render('Front/utilisateur/SignUp.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



   

}