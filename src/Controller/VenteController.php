<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Conversation;
use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\AgenceRepository;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VenteController extends AbstractController
{
    #[Route('/vente', name: 'app_vente')]
    public function index(Request $request, EntityManagerInterface $entityManager, AgenceRepository $agenceRepository): Response
    {
        $voiture = new Voiture();
        $agences = $agenceRepository->findAllExceptDefault();

        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture, [
            'agences' => $agences,
        ]);

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
            $user = $this->getUser();

            $voiture->setProprietaire($user);
            $voiture->setPrixLocationParJour(0);
            $voiture->setEstEnVente(true);
            $voiture->setEstALouer(false);

            $entityManager->persist($voiture);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('vente/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/voiture/{id}/changer_agence', name :'app_changer_agence_vente')]
    public function changerAgenceVente(EntityManagerInterface $entityManager, Request $request, Voiture $voiture): Response
    {
        $nouvelleAgenceId = $request->request->get('nouvelle_agence');

        $nouvelleAgence = $entityManager->getRepository(Agence::class)->find($nouvelleAgenceId);

        $voiture->setAgence($nouvelleAgence);
        $entityManager->flush();

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/vente/supprimer/{id}', name: 'app_vente_supprimer')]
    public function supprimer(Request $request,Filesystem $filesystem, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $voiture->getProprietaire()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer cette vente.");
        }

        $imagePath = $this->getParameter('upload_directory') . '/' . $voiture->getImage();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        // Supprimer la location
        $entityManager->remove($voiture);
        $entityManager->flush();

        // Redirection vers une autre page
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/{id}/contact', name: 'app_contact_vendeur')]
    public function contactVendeur(Request $request, Voiture $voiture, ConversationRepository $conversationRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le vendeur de la voiture
        $vendeur = $voiture->getProprietaire();
        $user = $this->getUser();
        // Vérifier s'il existe déjà une conversation entre l'utilisateur actuel et le vendeur
        $conversation = $conversationRepository->findConversation($this->getUser(), $vendeur);

        // Si une conversation existe, rediriger vers la page de la conversation
        if ($conversation) {
            return $this->redirectToRoute('app_message_show', [
                'id' => $conversation->getId(),
                'user' => $user,
            ]);
        }

        // Sinon, créer une nouvelle conversation et rediriger vers la page de la conversation
        $conversation = new Conversation();
        $conversation->setFirstUser($this->getUser());
        $conversation->setSecondUser($vendeur);
        $conversation->setDateCreation(new \DateTime());

        $entityManager->persist($conversation);
        $entityManager->flush();

        return $this->redirectToRoute('app_message_show', ['id' => $conversation->getId()]);
    }
}
