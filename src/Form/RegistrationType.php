<?php
// src/Form/RegistrationType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    IntegerType, TextType, EmailType, ChoiceType, FileType, PasswordType, SubmitType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{NotBlank, Length};
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use App\Entity\Utilisateur;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cin', IntegerType::class, [
                'label' => 'CIN',
                'constraints' => [new NotBlank()],
            ])
            ->add('nom', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('num_tel', TextType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [new NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'constraints' => [new NotBlank()],
            ])
            ->add('photo_utilisateur', FileType::class, [
                'label' => 'Photo de profil (facultatif)',
                'mapped' => false,
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    // Attention : PLUS DE EqualTo ici
                ],
            ])
            
        ;

        // *** Ajout d'un écouteur pour vérifier que les 2 mots de passe sont identiques ***
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError('Les mots de passe doivent correspondre.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
