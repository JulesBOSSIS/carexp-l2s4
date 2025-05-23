<?php

namespace App\Controller;

use App\Form\ProfilImageType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(AgenceRepository $agenceRepository): Response
    {
        // Récupérer l'utilisateur actuel
        $utilisateur = $this->getUser();
        if(!$utilisateur){
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les ventes de l'utilisateur
        $ventes = $utilisateur->getVentes();

        // Récupérer les achats de l'utilisateur
        $achats = $utilisateur->getAchats();

        // Récupérer tous les véhicules de l'utilisateur
        $voitures = $utilisateur->getVoitures();

        $locations = $utilisateur->getLocations();

        $anciennesLocations = [];
        $nouvellesLocations = [];

        $dateDuJour = new \DateTime();

        foreach ($locations as $location) {
            if ($location->getDateFinLocation() < $dateDuJour) {
                $anciennesLocations[] = $location;
            } else {
                $nouvellesLocations[] = $location;
            }
        }

        $agences = $agenceRepository->findAllExceptDefault();

        return $this->render('profil/index.html.twig', [
            'ventes' => $ventes,
            'achats' => $achats,
            'voitures' => $voitures,
            'anciennesLocations' => $anciennesLocations,
            'nouvellesLocations' => $nouvellesLocations,
            'agences' => $agences,
            'dateDuJour' => $dateDuJour,
        ]);
    }

    #[Route('/profil/modifier', name: 'app_profil_modifier')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur){
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ProfilImageType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageProfil')->getData();

            // Si une image a été téléchargée
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacez le fichier dans le répertoire où les images de profil sont stockées
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory_3'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le téléchargement échoue
                }

                // Mettez à jour le chemin de l'image de profil de l'utilisateur
                $utilisateur->setImageProfil($newFilename);
            }
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
