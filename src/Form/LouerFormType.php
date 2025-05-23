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
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class LouerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('agenceLoueuse', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'adresse',
                'choices' => $options['agences'],
            ])
            ->add('dateDebutLocation', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(),
                        'message' => 'La date de début doit être postérieure à la date d\'aujourd\'hui.'
                    ])
                ]
            ])
            ->add('dateFinLocation', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThan([
                        'value' => new \DateTime(),
                        'message' => 'La date de fin doit être postérieure à la date de début.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'agences' => []
        ]);
    }
}
