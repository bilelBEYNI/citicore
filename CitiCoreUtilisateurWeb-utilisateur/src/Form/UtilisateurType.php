<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('Cin', TextType::class, [
        'constraints' => [
            new Assert\NotBlank(),
            new Assert\Length(['min' => 8, 'max' => 8]),
            new Assert\Type('numeric'),
        ]
    ])
            ->add('Nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type("string")
                ]
            ])
            ->add('Prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type("string")
                ]
            ])
            ->add('Num_Tel', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 8]),
                    new Assert\Type("numeric")
                ]
            ])
            ->add('Email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('Genre', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('Role', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'Admin',
                    'Organisateur' => 'Organisateur',
                    'Participant' => 'Participant',
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('Mot_De_Passe', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6])
                ]
            ])
            ->add('Photo_Utilisateur', FileType::class, [
                'label' => 'Photo de l\'utilisateur',
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)',
                    ])
                ],
                'data_class' => null,  // Add this line to prevent expecting a "File" object in the entity
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}