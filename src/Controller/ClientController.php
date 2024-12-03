<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/moncompte', name: 'client.index')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig');
    }

    #[Route('/register', name: 'client.register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHash): Response {

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHash->hashPassword($client, $client->getPassword());
            $client->setPassword($hashedPassword);

            $em->persist($client);
            $em->flush();
            return $this->redirectToRoute('client.index');
        }

        return $this->render('client/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/login', name: 'client.login')]
    public function login(): Response {
        $form = $this->createForm(ClientType::class, );

        return $this->render('client/login.html.twig', [
            'form' => $form,
        ]);
    }


}
