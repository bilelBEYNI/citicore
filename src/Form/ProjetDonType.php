<?php

namespace App\Form;

use App\Entity\ProjetDon;
use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProjetDonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('objectif', NumberType::class, [
                'required' => true,
                'scale' => 2,  // Optional: set the decimal places
                'attr' => ['min' => 0],  // Optional: set a minimum value
            ])
            ->add('date_debut', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
            ])
            ->add('association', ChoiceType::class, [
                'choices' => $options['associations'],  // Use the passed associations
                'mapped' => true,  // Map the selected association to the entity's field
                'choice_label' => function (?Association $association) {
                    return $association ? $association->getNom() : '';  // Show the association's name
                },
                'choice_value' => 'Nom',  // Use the ID to map to the correct entity
                'placeholder' => 'Choose an association',  // Placeholder text for the dropdown
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjetDon::class,
            'associations' => [],  // Default empty array for associations
        ]);
    }
}

