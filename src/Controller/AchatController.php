<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Entity\Voiture;
use App\Form\RechercheVoitureType;
use App\Repository\AgenceRepository;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AchatController extends AbstractController
{
    #[Route('/achat', name: 'app_achat')]
    public function index(Request $request, VoitureRepository $voitureRepository, AgenceRepository $agenceRepository): Response
    {
        $agences = $agenceRepository->findAllExceptDefault();
        // Créer le formulaire de recherche
        $form = $this->createForm(RechercheVoitureType::class, null, [
            'agences'=>$agences,
        ]);
        $form->handleRequest($request);

        // Traitement de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $voitures = $voitureRepository->findByCriteres($data);

            return $this->render('achat/index.html.twig', [
                'form' => $form->createView(),
                'voitures' => $voitures,
            ]);
        }

        $premierVoiture = $voitureRepository->findNumberVente(10);
        return $this->render('achat/index.html.twig', [
            'form' => $form->createView(),
            'voitures' => $premierVoiture,
        ]);
    }

    #[Route('/achat/{id}/acheter', name: 'app_acheter')]
    public function acheter(VoitureRepository $voitureRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            // Si l'utilisateur n'est pas authentifié, rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
        $voiture = $voitureRepository->find($id);

        if (!$voiture) {
            return $this->redirectToRoute('app_homepage');
        }

        // Récupérer l'utilisateur actuel (acheteur)
        $acheteur = $this->getUser();

        $agence = $voiture->getAgence();

        // Création d'une nouvelle entité Vente
        $vente = new Vente();
        $vente->setVendeur($voiture->getProprietaire());
        $vente->setAcheteur($acheteur);
        $vente->setVoiture($voiture);
        $vente->setIntermediaire($agence);
        $vente->setPrixVente($voiture->getPrixVente());
        $vente->setDateVente(new \DateTime());

        // Mettre à jour l'entité Voiture
        $voiture->setProprietaire(null);
        $voiture->setAgence(null);
        $voiture->setEstEnVente(false);
        $voiture->setEstALouer(false);

        // Enregistrer les modifications dans la base de données
        $entityManager->persist($vente);
        $entityManager->flush();

        return $this->render('achat/confirmation.html.twig', [
            'vehicule' =>$voiture,
        ]);
    }

}
