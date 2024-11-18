<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FrontController extends AbstractController {

    #[Route('/', name:'index')]
    function index(Request $request, EntityManagerInterface $entityManager): Response {
        // Récupère les 3 derniers articles
        $lastArticles = $entityManager->getRepository(Article::class)
            ->findBy([], ['createdAt' => 'DESC'], 3);

        $flashArticles = $entityManager->getRepository(Article::class)
            ->findBy([], ['flashOrNo' => 'DESC'], 3);


        return $this->render('front/index.html.twig', [
            'latestArticles' => $lastArticles,
            'flashArticles' => $flashArticles,
        ]);
    }
}
