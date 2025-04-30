<?php


namespace App\Form;

use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Utilisateur_id', TextType::class, [
                'label' => 'ID Utilisateur'
            ])
            ->add('contenu', TextType::class, [
                'label' => 'Contenu'
            ])
            ->add('date_demande', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la demande'
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'Traitée' => 'traitee',
                    'Rejetée' => 'rejetee',
                ],
                'label' => 'Statut'
            ])
            ->add('ajouter', SubmitType::class, [
                'label' => 'Ajouter la demande'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
