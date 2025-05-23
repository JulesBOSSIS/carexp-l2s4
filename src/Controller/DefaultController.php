<?php

namespace App\Controller;

use App\Form\RechercheVoitureLocationType;
use App\Repository\AgenceRepository;
use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
        ]);
    }
    #[Route('/voitures', name: 'app_voiture')]
    public function voiture(VoitureRepository $voitureRepository): Response
    {
        $voituresVente = $voitureRepository->findNumberVente(15);
        $voituresLocation = $voitureRepository->findNumberLocation(15);
        return $this->render('default/voiture.html.twig', [
            'voituresVente'=>$voituresVente,
            'voitureLocation'=>$voituresLocation,
        ]);
    }

    #[Route('/voiture/{id}', name: 'app_voir_voiture')]
    public function voirVoiture($id, VoitureRepository $voitureRepository, Security $security): Response
    {
        $voiture = $voitureRepository->findOneByIdDispoVente($id);
        $vehicules = $voitureRepository->findNumberVente(10);

        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        return $this->render('vente/voiture.html.twig', [
            'voiture' => $voiture,
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/api/data/voiture', name: 'api_data_voiture', methods: ['GET'])]
    public function getData(VoitureRepository $voitureRepository, SerializerInterface $serializer): JsonResponse
    {
        // Récupérez les données des locations depuis votre base de données
        $cars = $voitureRepository->findAllVente();

        return $this->json($cars,Response::HTTP_OK,[],
            [
                // voir la définition des groupes dans l'entité
                'groups' => ['venteApi']
            ]
        );
    }
    #[Route('/api/data/agence', name: 'api_data_agence', methods: ['GET'])]
    public function getDataAgence(AgenceRepository $agenceRepository,SerializerInterface $serializer): JsonResponse
    {
        $agencies = $agenceRepository->findAllExceptDefault();
        return $this->json($agencies,Response::HTTP_OK,[],
            [
                // voir la définition des groupes dans l'entité
                'groups' => ['location']
            ]
        );
    }
}
