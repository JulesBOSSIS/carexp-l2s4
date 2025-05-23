<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('admin/index.html.twig', [
        ]);
    }

    #[Route('/api/locations', name: 'api_locations_list', methods: ['GET'])]
    public function listLocations(LocationRepository $locationRepository): JsonResponse
    {
        // Récupérez les données des locations depuis votre base de données
        $locations = $locationRepository->findAll();

        return $this->json($locations,Response::HTTP_OK,[],
            [
                // voir la définition des groupes dans l'entité
                'groups' => ['location']
            ]
        );
    }
}
