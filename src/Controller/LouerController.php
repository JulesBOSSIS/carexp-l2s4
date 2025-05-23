<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Voiture;
use App\Form\LouerFormType;
use App\Repository\AgenceRepository;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LouerController extends AbstractController
{
    #[Route('/louer', name: 'app_louer')]
    public function index(Request $request, VoitureRepository $voitureRepository, AgenceRepository $agenceRepository): Response
    {
        $agences = $agenceRepository->findAllExceptDefault();
        $form = $this->createForm(LouerFormType::class, null, [
            'agences' => $agences,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            $agence = $data->getAgenceLoueuse();
            $dateDebut = $data->getDateDebutLocation();
            $dateFin = $data->getDateFinLocation();

            // Vérifier si la date de fin est antérieure à la date de début
            if ($dateFin < $dateDebut) {
                $temp = $dateDebut;
                $dateDebut = $dateFin;
                $dateFin = $temp;
            }

            $vehiculesDisponibles = $voitureRepository->findAllDisponibleLocation(
                    $agence,
                    $dateDebut,
                    $dateFin
                );
            $nbJours = $dateDebut->diff($dateFin)->days;

            return $this->render('louer/resultat.html.twig', [
                'vehicules' => $vehiculesDisponibles,
                'nbJours' => $nbJours,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
            ]);
        }

        return $this->render('louer/index.html.twig', [
            'form' => $form->createView(),
            'vehicules'=>[],
            'nbJours' => null,
        ]);
    }

    #[Route('/location/{id}/louer', name: 'app_louer_confirmation')]
    public function louer(Request $request, VoitureRepository $voitureRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $voiture = $voitureRepository->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('La voiture demandée n\'existe pas');
        }

        // Récupérer l'utilisateur actuel (locataire)
        $loueur = $this->getUser();

        $agence = $voiture->getAgence();

        $dateDebut = new \DateTime($request->query->get('dateDebut'));
        $dateFin = new \DateTime($request->query->get('dateFin'));

        $nbJours = $dateDebut->diff($dateFin)->days;
        $prixTotal = $voiture->getPrixLocationParJour()*$nbJours;
        // Création d'une nouvelle entité Location
        $location = new Location();
        $location->setVoitureLoue($voiture);
        $location->setLocataire($loueur);
        $location->setAgenceLoueuse($agence);
        $location->setDateDebutLocation($dateDebut);
        $location->setDateFinLocation($dateFin);
        $location->setPrixTotal($prixTotal);

        // Enregistrer les modifications dans la base de données
        $entityManager->persist($location);
        $entityManager->flush();

        return $this->render('achat/confirmation.html.twig', [
            'vehicule' => $voiture,
        ]);
    }


    #[Route('/louer/supprimer/{id}', name: 'app_louer_supprimer')]
    public function supprimer(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les autorisations (par exemple, vérifier si l'utilisateur est le locataire de la location)
        if ($this->getUser() !== $location->getLocataire()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer cette location.");
        }

        // Supprimer la location
        $entityManager->remove($location);
        $entityManager->flush();

        // Redirection vers une autre page
        return $this->redirectToRoute('app_profil');
    }
}
