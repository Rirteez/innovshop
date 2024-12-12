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
        $perPage = 16;

        $filterBy = $request->query->all('filterBy', []);
        $sortBy = $request->query->get('sortBy');

        $articles = $repository->paginateArticles($page, $perPage, $filterBy, $sortBy);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'maxPage' => ceil($articles->getTotalItemCount() / $perPage),
            'page' => $page,
            'filterBy' => $filterBy,
            'sortBy' => $sortBy,
        ]);
    }

    #[Route('/catalog/{slug}-{id}', name: 'article.show', requirements:['id' => '\d+', 'slug' => '[a-z0-9-_]+'])]
    public function show(Request $request, string $slug, int $id, ArticleRepository $repository): Response
    {
        $article = $repository->find($id);
        $articlesRandom = [];
        $categories = $article->getCategories();


        if(count($categories) > 0){
            $categoryID = $categories[0]->getId();
            $articlesRandom = $repository->findTwoByRandom($categoryID, $article->getId());
        }

        if ($article->getSlug() !== $slug) {
            return $this->redirectToRoute('article.show', ['slug' => $article->getSlug(), 'id' => $article->getId()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'articlesRandom' => $articlesRandom,
        ]);
    }
}
