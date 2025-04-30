<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_evenement', TextType::class, [
                'label' => 'Nom de l\'événement',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '3'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de l\'événement est requis'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('date_evenement', DateTimeType::class, [
                'label' => 'Date et heure',
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date est requise'
                    ])
                ]
            ])
            ->add('lieu_evenement', TextType::class, [
                'label' => 'Lieu',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '3'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le lieu est requis'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le lieu doit contenir au moins {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'label' => 'Catégorie',
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La catégorie est requise'
                    ])
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'needs-validation'
            ]
        ]);
    }
}
