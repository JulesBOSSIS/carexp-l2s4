<?php

namespace App\Form;

use App\Entity\Agence;
use App\Entity\Location;
use App\Entity\User;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebutLocation', null, [
                'widget' => 'single_text',
            ])
            ->add('dateFinLocation', null, [
                'widget' => 'single_text',
            ])
            ->add('prixTotal')
            ->add('locataire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('agenceLoueuse', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'adresse',
            ])
            ->add('voitureLoue', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => function (Voiture $voiture) {
                    return sprintf('%s - %s', $voiture->getMarque(), $voiture->getModele());
                },
                'choices' => $options['voitures'],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'voitures' => [],
        ]);
    }
}
