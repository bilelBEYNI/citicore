<?php

namespace App\Controller\UtilisateurController;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UtilisateurType;
use App\Form\FeedbackType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\FeedbackRepository;
use App\Entity\Feedback;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;  // ← ici
use Symfony\Component\Mailer\MailerInterface; 
use Symfony\Component\String\Slugger\SluggerInterface;    
use Symfony\Component\Mime\Email;    

class UtilisateurController extends AbstractController
{ 
    

    
#[Route('/dashboard/utilisateurs', name: 'app_user_index')]
public function utilisateurindex(UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
{
    // Récupérer le nombre total de participants
    $participantCount = $entityManager->getRepository(Utilisateur::class)
        ->count(['Role' => 'Participant']);
    // Récupérer le nombre total d'organisateurs
    $organisateurCount = $entityManager->getRepository(Utilisateur::class)
        ->count(['Role' => 'Organisateur']);
    // Récupérer le nombre d'hommes
    $hommesCount = $entityManager->getRepository(Utilisateur::class)
        ->count(['Genre' => 'Homme']);
    // Récupérer le nombre de femmes
    $femmesCount = $entityManager->getRepository(Utilisateur::class)
        ->count(['Genre' => 'Femme']);
    
    // Récupérer tous les utilisateurs
    $utilisateurs = $utilisateurRepository->findAll();
    
    // Passer les données à la vue
    return $this->render('back/utilisateur/Utilisateur.html.twig', [
        'utilisateurs' => $utilisateurs,
        'participantCount' => $participantCount,
        'organisateurCount' => $organisateurCount,
        'genderData' => [
            'homme' => $hommesCount,
            'femme' => $femmesCount,
        ],
        'roleData' => [
            'organisateur' => $organisateurCount,
            'participant' => $participantCount,
        ],
    ]);
}


    

#[Route('/dashboard/utilisateur/ajouter/{Cin}', name: 'feedback_add')]
public function AjouterFeedBack(int $Cin, Request $request, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepository): Response
{
    
    $organisateur = $utilisateurRepository->findOneBy(['Cin' => $Cin]);

    if (!$organisateur) {
        throw $this->createNotFoundException('Organisateur non trouvé.');
    }
    
    
    $feedback = new Feedback();
    $feedback->setCin_Organisateur($Cin); 
    $user = $this->getUser();
    if ($user) {
        $feedback->setCin_Participant($user->getCin()); 
    } else {
        throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un feedback.');
    }
    $form = $this->createForm(FeedbackType::class, $feedback);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($feedback);
        $em->flush();

        $this->addFlash('success', 'Feedback ajouté avec succès.');
        return $this->redirectToRoute('participant_dashboard');
    }

    return $this->render('back/utilisateur/addfeedback.html.twig', [
        'form' => $form->createView(),
        'organisateur' => $organisateur,
    ]);
}

    #[Route('/dashboard/utilisateur/show/{CIN}', name: 'app_user_show')]
    public function show(int $CIN, UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateur = $utilisateurRepository->find($CIN);
    
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
    
        return $this->render('back/utilisateur/ShowUtilisateur.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
    

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard/utilisateur/edit/{CIN}', name: 'app_user_edit')]
    public function edit($CIN, UtilisateurRepository $utilisateurRepository, Request $request): Response
    {
        // Retrieve the user by CIN
        $utilisateur = $utilisateurRepository->find($CIN);

        // Check if the user exists
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        // Create the form and add the submit button before handling the request
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('Modifier', SubmitType::class, [
            'label' => 'Modifier',
            'attr' => ['class' => 'btn btn-primary']  // Optional: for styling
        ]);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist changes to the database
            $this->entityManager->flush();

            // Add a flash message and redirect
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_user_index');
        }

        // Render the form
        return $this->render('back/utilisateur/edit.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    
    #[Route('/dashboard/utilisateur/delete/{CIN}', name: 'app_user_delete')]
    public function delete(int $CIN, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        // Trouver l'utilisateur par son CIN
        $user = $utilisateurRepository->find($CIN);
    
        // Si l'utilisateur n'existe pas, afficher une erreur (par exemple, redirection avec message d'erreur)
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_user_index');
        }
    
        // Suppression de l'utilisateur
        $em->remove($user);
        $em->flush();
    
        // Redirection vers la liste des utilisateurs
        return $this->redirectToRoute('app_user_index');
    }
    

 

    #[Route('/dashboard/utilisateur/ajouter', name: 'app_user_new')]
    public function newUser(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer,
        SluggerInterface $slugger
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1) upload de la photo, si présente
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoUtilisateur')->getData();
            if ($photoFile) {
                $safeName    = $slugger->slug(pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeName.'-'.uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $user->setPhotoUtilisateur($newFilename);
            }

            // 2) génération aléatoire du mot de passe (8 caractères)
            $plainPassword = substr(str_shuffle(
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
            ), 0, 8);

            // 3) hash du mot de passe
            $hashed = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashed);

            // 4) persistance
            $em->persist($user);
            $em->flush();

            // 5) envoi de l'email
            $email = (new Email())
                ->from('achrefkachai023@gmail.com')
                ->to($user->getEmail())
                ->subject('Création de votre compte CitiCore')
                ->text(
                    "Bonjour {$user->getPrenom()},\n\n".
                    "Votre compte a été créé. Votre mot de passe temporaire est : $plainPassword\n\n".
                    "Pensez à le changer dès votre première connexion."
                )
            ;
            $mailer->send($email);

            $this->addFlash('success', 'Utilisateur ajouté et mail envoyé avec son mot de passe.');
            
        }

        return $this->render('back/utilisateur/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


//-------------------------------------feedback-------------------------------------//
#[Route('/dashboard/feedback', name: 'app_feedback_index')]
public function showFeedbacks(FeedbackRepository $feedbackRepository): Response
{
    
    $feedbacks = $feedbackRepository->findAll();

    return $this->render('back/utilisateur/FeedBack.html.twig', [
        'feedbacks' => $feedbacks
    ]);
}

}
