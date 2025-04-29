<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Sujet')
        
        ->add('Description')

        ->add('Date_Creation', null, [
            'widget' => 'single_text',
        ])

        ->add('Date_Resolution', null, [
            'widget' => 'single_text',
        ])

        ->add('Type_Reclamation', ChoiceType::class, [
            'choices'  => [
                'Marketplace' => 'Marketplace',
                'Demande' => 'Demande',
                'Evenement' => 'Evenement',
                'ProjetDons' => 'ProjetDons',
            ],
            'placeholder' => 'Choisissez un type',
        ])
        ->add('Cin_Utilisateur');
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
