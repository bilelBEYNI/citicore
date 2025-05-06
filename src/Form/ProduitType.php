<?php
// src/Form/ProduitType.php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    TextareaType,
    MoneyType,
    IntegerType,
    DateType,
    FileType
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{
    NotBlank,
    Length,
    Positive,
    File
};

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (DZD)',
                'currency' => 'DZD',
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire.']),
                    new Positive(['message' => 'Le prix doit être supérieur à 0.']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('vendeurId', IntegerType::class, [
                'label' => 'ID du vendeur',
                'constraints' => [
                    new NotBlank(['message' => 'L\'identifiant du vendeur est obligatoire.']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateAjout', DateType::class, [
                'label' => "Date d'ajout",
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo du produit (optionnelle)',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '2m',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG ou GIF).',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Produit::class]);
    }
}