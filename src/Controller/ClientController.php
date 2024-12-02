<?php

namespace App\Controller;

use App\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function register(): Response {
        $form = $this->createForm(ClientType::class, );

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
