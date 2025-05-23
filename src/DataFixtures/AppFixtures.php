<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use App\Entity\Location;
use App\Entity\User;
use App\Entity\Voiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $agenceDefaut = new Agence();
        // $agenceDefaut->setNom("Aucune Agence");
        // $agenceDefaut->setAdresse("Aucune Adresse");
        // $agenceDefaut->setNumTel("##########");
        // $manager->persist($agenceDefaut);

        // $utilisateurDefaut = new User();
        // $utilisateurDefaut->setName("DefaultUserName");
        // $utilisateurDefaut->setFirstName("DefaultUserFirstName");
        // $utilisateurDefaut->setEmail("defaultUser@user.com");
        // $utilisateurDefaut->setPassword("motDePasse");
        // $utilisateurDefaut->setRoles(['ROLE_USER']);
        // $manager->persist($utilisateurDefaut);

        // for ($i = 0; $i < 11; $i++) {
        //     $agence = new Agence();
        //     $agence->setNom("LocaCar".$i);
        //     $agence->setAdresse($i." Rue des LocaCars");
        //     $agence->setNumTel("".rand(1000000000, 9999999999));
        //     $manager->persist($agence);
        //     $this->setReference('agence'.$i, $agence);
        // }

        // for ($i = 0; $i < 100; $i++) {
        //     $voiture = new Voiture();
        //     $voiture->setMarque("Totota");
        //     $voiture->setModele(rand(10, 20)."TOTO");
        //     $voiture->setanneeMiseEnCirculation(new \DateTime(mt_rand(-30,+0).' year'));
        //     $voiture->setKilometrage(rand(100, 100000));
        //     $voiture->setEstALouer(true);
        //     $voiture->setEstEnVente(false);
        //     $voiture->setImages([rand(1, 15).".jpg", rand(1, 15).".jpg", rand(1, 15).".jpg"]);
        //     $voiture->setAgence($this->getReference('agence'.rand(0, 10)));
        //     $voiture->setPrixLocationParJour(rand(100, 250));
        //     $voiture->setPrixVente(0);
        //     $voiture->setEmissons(rand(90, 110));
        //     $voiture->setNombreDePlaces(rand(2, 5));
        //     $voiture->setClim(rand(0, 1));
        //     $manager->persist($voiture);
        // }



        // for ($i = 0; $i <10; $i++){
        //     $utilisateur = new User();
        //     $utilisateur->setRoles(['ROLE_USER']);
        //     $utilisateur->setPassword(rand(10, 9999)."test");
        //     $utilisateur->setEmail("test".$i."@gmail.com");
        //     $utilisateur->setName("Test".$i);
        //     $utilisateur->setFirstName("Fname".$i);
        //     $manager->persist($utilisateur);
        //     $this->setReference('user'.$i, $utilisateur);
        // }
        for ($i = 11; $i < 20; $i++) {
            $agence = new Agence();
            $agence->setNom("Locacar".$i);
            $agence->setAdresse($i." Rue des Nouvelles Agences");
            $agence->setNumTel("".rand(1000000000, 9999999999));
            $manager->persist($agence);
            $this->setReference('agence'.$i, $agence);
        }

         for ($i = 0; $i < 100; $i++) {
             $voiture = new Voiture();
             $voiture->setMarque("FORT");
             $voiture->setModele(rand(10, 30)."FOR");
             $voiture->setanneeMiseEnCirculation(new \DateTime(mt_rand(-30,+0).' year'));
             $voiture->setKilometrage(rand(1000, 10000));
             $voiture->setEstALouer(true);
             $voiture->setEstEnVente(false);
             $voiture->setImages([rand(1, 15).".jpg", rand(1, 15).".jpg", rand(1, 15).".jpg"]);
             $voiture->setAgence($this->getReference('agence'.rand(11, 19)));
             $voiture->setPrixLocationParJour(rand(100, 250));
             $voiture->setPrixVente(0);
             $voiture->setEmissons(rand(90, 110));
             $voiture->setNombreDePlaces(rand(2, 5));
             $voiture->setClim(rand(0, 1));
             $manager->persist($voiture);
             $this->setReference('voiture'.$i, $voiture);
         }
        
        for ($i = 10; $i < 20; $i++) {
            $utilisateur = new User();
            $utilisateur->setRoles(['ROLE_USER']);
            $utilisateur->setPassword(rand(10, 9999)."test");
            $utilisateur->setEmail("test".$i."@gmail.com");
            $utilisateur->setName("Test".$i);
            $utilisateur->setFirstName("Fname".$i);
            $manager->persist($utilisateur);
            $this->setReference('user'.$i, $utilisateur);
        }


        for ($i = 0; $i < 100; $i++) {
            $location = new Location();
            $location->setDateDebutLocation(new \DateTime(rand(-30, +60).' days'));
            $location->setDateFinLocation(new \DateTime(rand(61, 100).' days'));
            $location->setVoitureLoue($this->getReference('voiture'.$i));
            $location->setLocataire($this->getReference('user'.rand(10, 19)));
            $location->setAgenceLoueuse($this->getReference('agence'.rand(11, 19)));
            $startDate = $location->getDateDebutLocation();
            $endDate = $location->getDateFinLocation();
            $rentalPricePerDay = $location->getVoitureLoue()->getPrixLocationParJour();
            $numberOfDays = $endDate->diff($startDate)->days;
            $totalPrice = $rentalPricePerDay * $numberOfDays;
            $location->setPrixTotal($totalPrice);
            $manager->persist($location);
        }
        $manager->flush();
    }
}