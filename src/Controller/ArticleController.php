<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/catalog', name: 'article.index')]
    public function index(Request $request, ArticleRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);
        $perPage = 4;

        $articles = $repository->paginateArticles($page, $perPage);
        $maxPage = ceil($articles->getTotalItemCount() / $perPage);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'maxPage' => $maxPage,
            'page' => $page,
        ]);
    }

    #[Route('/catalog/{slug}-{id}', name: 'article.show', requirements:['id' => '\d+', 'slug' => '[a-z0-9-_]+'])]
    public function show(Request $request, string $slug, int $id, ArticleRepository $repository): Response
    {
        $article = $repository->find($id);
        if ($article->getSlug() !== $slug) {
            return $this->redirectToRoute('article.show', ['slug' => $article->getSlug(), 'id' => $article->getId()]);
        }
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/catalog/{id}/edit', name: 'article.edit', requirements:['id' => '\d+', ])]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $filename = $article->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/images/uploads', $filename);
            $article->setImage($filename);
            $em->flush();
            $this->addFlash('success', 'Modification réussie');
            return $this->redirectToRoute('article.index');
        }
        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'formEditArticle' => $form
        ]);
    }
}
