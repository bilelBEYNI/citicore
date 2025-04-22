<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Liste des régions de la Tunisie
        $regionsTunisie = [
            "Tunis", "Sousse", "Sfax", "Ariana", "Ben Arous", "Bizerte", "Gabès", "Gafsa", "Kairouan",
            "Kasserine", "Kébili", "Mahdia", "Manouba", "Medenine", "Monastir", "Nabeul", "Sidi Bouzid",
            "Siliana", "Tataouine", "Tozeur", "Zaghouan"
        ];

        $builder
            ->add('nom_evenement', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de l\'événement est requis.'
                    ]),
                    new Assert\Type([
                        'type' => 'string',
                        'message' => 'Le nom de l\'événement doit être une chaîne de caractères valide.'
                    ])
                ]
            ])
            ->add('date_evenement', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de l\'événement est requise.'
                    ]),
                    new Assert\Date([
                        'message' => 'La date de l\'événement doit être valide.'
                    ])
                ]
            ])
            ->add('lieu_evenement', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le lieu de l\'événement est requis.'
                    ]),
                    new Assert\Choice([
                        'choices' => $regionsTunisie,
                        'message' => 'Le lieu de l\'événement doit être une région valide de la Tunisie.',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
