<?php

namespace App\Form;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('reclamation', EntityType::class, [
            'class' => Reclamation::class,
            'choice_label' => 'Sujet', // ou autre champ de la réclamation
            'label' => 'Réclamation associée',
            'placeholder' => 'Choisir une réclamation',
            'required' => true,
        ])

        ->add('contenu')

        ->add('DateReponse', DateTimeType::class, [
           'label' => 'Date de réponse',
            'required' => false,
            'widget' => 'single_text',
            'html5' => true,
        ])
            
            ->add('Statut', ChoiceType::class, [
                'label' => 'Statut de la réponse',
                'choices' => [
                    'Rejetée' => 'Rejetée',
                    'Traitée' => 'Traitée',
                    'En Cours' => 'En Cours',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
