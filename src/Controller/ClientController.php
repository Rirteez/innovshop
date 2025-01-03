<?php

namespace App\Controller;

use App\Form\ClientType;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/moncompte', name: 'client.index')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var Client $client */
        $client = $this->getUser();

        // Formulaire pour l'adresse
        $formAdresse = $this->createForm(ClientType::class, $client);
        $formAdresse->handleRequest($request);

        // Formulaire pour les informations personnelles
        $formClient = $this->createForm(RegisterType::class, $client, [
            'add_submit' => false,
        ]);
        $formClient->handleRequest($request);

        // Gestion du formulaire d'adresse
        if ($formAdresse->isSubmitted() && $formAdresse->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations de livraison ont été mises à jour.');
            return $this->redirectToRoute('client.index');
        }

        // Gestion du formulaire d'informations personnelles
        if ($formClient->isSubmitted() && $formClient->isValid()) {

            // Hachage du mot de passe si modifié
            $newPassword = $formClient->get('password')->getData();

            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($client, $newPassword);
                $client->setPassword($hashedPassword);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Vos informations personnelles ont été mises à jour.');
            return $this->redirectToRoute('client.index');
        }

        return $this->render('client/index.html.twig', [
            'formAdresse' => $formAdresse->createView(),
            'formClient' => $formClient->createView(),
        ]);
    }
}
