<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(ConversationRepository $conversationRepository): Response
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur){
            return $this->redirectToRoute('app_login');
        }

        // Récupérer toutes les conversations de l'utilisateur
        $conversations = $conversationRepository->findConversationsSortedByDernierMessageDate($utilisateur->getId());

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
            'user' => $utilisateur,
        ]);
    }

    #[Route('/message/{id}', name: 'app_message_show')]
    public function show(Request $request, $id, ConversationRepository $conversationRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        if(!$utilisateur){
            return $this->redirectToRoute('app_login');
        }
        $conversation = $conversationRepository->findConversationDisponible($id);
        if(!$conversation){
            return $this->redirectToRoute('app_message');
        }
        // Vérifier si l'utilisateur a accès à cette conversation
        $utilisateur = $this->getUser();
        if (!$conversation->accesUser($utilisateur)) {
            return $this->redirectToRoute('app_profil');
        }

        // Marquer tous les messages non lus comme lus
        $messages = $conversation->getMessages();

        foreach ($messages as $message) {
            if (!$message->isLu() && $message->getDestinataire() === $this->getUser()) {
                $message->setLu(true);
            }
        }
        // Enregistrer les modifications
        $entityManager->flush();

        // Créer un nouveau message
        $message = new Message();
        $message->setConversation($conversation);
        $message->setExpediteur($utilisateur);

        // Créer un formulaire pour ajouter un nouveau message
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDestinataire($conversation->getAutreUtilisateur($utilisateur));
            $message->setDate(new \DateTime());
            $message->setLu(false);

            $image = $form->get('image')->getData();
            if($image){
                $newFilename = uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('upload_directory_2'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception
                }

                $message->setImage($newFilename);
            }

            // Sauvegarder le message dans la base de données
            $entityManager->persist($message);
            $entityManager->flush();

            // Rediriger vers la même page pour actualiser la conversation avec le nouveau message
            return $this->redirectToRoute('app_message_show', ['id' => $conversation->getId()]);
        }

        return $this->render('message/show.html.twig', [
            'conversation' => $conversation,
            'user' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }
}
