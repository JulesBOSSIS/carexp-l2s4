<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/agence')]
class AgenceController extends AbstractController
{
    #[Route('/', name: 'app_agence_index', methods: ['GET'])]
    public function index(AgenceRepository $agenceRepository): Response
    {
        return $this->render('agence/index.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_agence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($agence);
            $entityManager->flush();

            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agence_show', methods: ['GET'])]
    public function show(Agence $agence): Response
    {
        return $this->render('agence/show.html.twig', [
            'agence' => $agence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_agence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agence $agence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('agence/edit.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_agence_delete', methods: ['POST'])]
    public function delete(Request $request, Agence $agence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->getPayload()->get('_token'))) {
            // Récupérer l'agence par défaut
            $agenceParDefaut = $entityManager->getRepository(Agence::class)->find(0);

            // Associer toutes les ventes de l'agence à supprimer à l'agence par défaut
            foreach ($agence->getVentes() as $vente) {
                $vente->setIntermediaire($agenceParDefaut);
            }

            $voitures = $agence->getVoitures();
            foreach ($voitures as $voiture) {
                $voiture->setAgence($agenceParDefaut);
            }

            $entityManager->remove($agence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/redistribuer', name: 'app_redistribuer_voitures')]
    public function redistribuerVoitures(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'agence par défaut
        $agenceParDefaut = $entityManager->getRepository(Agence::class)->find(0);

        $agences = $entityManager->getRepository(Agence::class)->findAllExceptDefault();

        $voitures = $agenceParDefaut->getVoitures();

        foreach ($voitures as $voiture) {
            $agence = $agences[array_rand($agences)];
            $voiture->setAgence($agence);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_agence_index');
    }
}
