<?php

namespace App\Form;

use App\Entity\Agence;
use App\Entity\User;
use App\Entity\Vente;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheVoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'required' => false,

            ])
            ->add('modele', TextType::class, [
                'label' => 'Modele',
                'required' => false,
            ])
            ->add('kilometrage', IntegerType::class,[
                'label' => 'Kilometrage',
                'required' => false,
            ])
            ->add('prixVente', IntegerType::class,[
                'label' => 'Prix',
                'required' => false
            ])
            ->add('agence', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'adresse',
                'choices' => $options['agences'],
                'required' =>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'agences' => [],
        ]);

    }
}
