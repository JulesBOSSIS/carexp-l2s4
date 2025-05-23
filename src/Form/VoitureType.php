<?php

namespace App\Form;

use App\Entity\Agence;
use App\Entity\Categorie;
use App\Entity\Voiture;
use PHPUnit\Framework\Constraint\LessThan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque')
            ->add('modele')
            ->add('anneeMiseEnCirculation', null, [
                'label' => 'Année de mise en circulation',
                'widget' => 'single_text',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => new \DateTime(),
                        'message' => 'La date doit être antérieur à la date d\'aujourd\'hui.'
                    ])
                ]

            ])
            ->add('kilometrage')
            ->add('prixVente')
            ->add('clim')
            ->add('nombreDePlaces')
            ->add('emissons')
            ->add('images', FileType::class, [
                'label' => 'Images du véhicule',
                'mapped' => false,
                'required' => false,
                'multiple' => true, // Pour upload plusieurs images
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => ['image/jpeg'],
                                'mimeTypesMessage' => 'Veuillez télécharger une image JPEG valide',
                            ])
                        ]
                    ])
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
            ])
            ->add('agence', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'adresse',
                'choices' => $options['agences'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
            'agences' => [],
        ]);
    }
}
