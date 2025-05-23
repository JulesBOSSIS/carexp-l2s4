<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\Voiture1Type;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/voiture/location/crud')]
class VoitureLocationCrudController extends AbstractController
{
    #[Route('/', name: 'app_voiture_location_crud_index', methods: ['GET'])]
    public function index(VoitureRepository $voitureRepository): Response
    {
        return $this->render('voiture_location_crud/index.html.twig', [
            'voitures' => $voitureRepository->findAllLocation(),
        ]);
    }

    #[Route('/new', name: 'app_voiture_location_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(Voiture1Type::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $newFilename = uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception
                }

                $voiture->addImage($newFilename);
            }
            $voiture->setEstEnVente(false);
            $voiture->setPrixVente(0);
            $entityManager->persist($voiture);
            $entityManager->flush();

            return $this->redirectToRoute('app_voiture_location_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture_location_crud/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voiture_location_crud_show', methods: ['GET'])]
    public function show(Voiture $voiture): Response
    {
        return $this->render('voiture_location_crud/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_voiture_location_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Voiture1Type::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_voiture_location_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture_location_crud/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voiture_location_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        $locations = $voiture->getLocations();
        foreach ($locations as $location) {
            $startDate = $location->getDateDebutLocation();
            $endDate = $location->getDateFinLocation();

            // Vérifie si la voiture est en location dans cet intervalle de dates
            if ($startDate <= new \DateTime() && new \DateTime() <= $endDate) {
                // La voiture est en location dans cet intervalle, donc ne peut pas être supprimée
                $this->addFlash('error', 'La voiture est actuellement en location et ne peut pas être supprimée.');
                return $this->redirectToRoute('app_location_index');
            }
        }

        if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
            $this->addFlash('success', 'La voiture a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide.');
        }

        return $this->redirectToRoute('app_voiture_location_crud_index');
    }

}
