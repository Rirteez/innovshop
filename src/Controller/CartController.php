<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ArticleRepository;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(CartRepository $cartRepository): Response
    {
        $client = $this->getUser();
        $cartItemCount = 0;

        if ($client instanceof Client) {
            $cart = $cartRepository->findOrCreateCart($client);
            $cartItemCount = $cartRepository->getCartItemsCount($cart);
        }

        return $this->render('base.html.twig', [
            'cartItemCount' => $cartItemCount,
        ]);
    }

    #[Route('/panier', name: 'app_cart')]
    public function index(CartRepository $cartRepository): Response
    {
        $client = $this->getUser();
        $cartItemCount = 0;

        if (!$client instanceof Client) {
            $cart = $cartRepository->findBy(['client' => $client]);
            $cartItemCount = $cartRepository->getCartItemsCount($cart);
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre panier.');
        }

        $cart = $cartRepository->findOrCreateCart($client);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'items' => $cart->getCartItems(),
            'cartItemCount' => $cartItemCount,
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request, CartRepository $cartRepository, ArticleRepository $articleRepository): JsonResponse {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return new JsonResponse(['error' => 'Vous devez être connecté.'], 403);
        }

        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article non trouvé.'], 404);
        }

        $quantity = $request->get('quantity', 1);
        $variant = $request->get('variant', null);

        $cart = $cartRepository->findOrCreateCart($client);
        $cartRepository->addItemToCart($cart, $article, (int) $quantity, $variant);

        return new JsonResponse(['success' => 'Article ajouté au panier.']);
    }

    #[Route('/panier/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id, Request $request, CartRepository $cartRepository, ArticleRepository $articleRepository): JsonResponse {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return new JsonResponse(['error' => 'Vous devez être connecté.'], 403);
        }

        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article non trouvé.'], 404);
        }

        $variant = $request->get('variant', null);
        $cart = $cartRepository->findOrCreateCart($client);
        $cartRepository->removeItemFromCart($cart, $article, $variant);

        return new JsonResponse(['success' => 'Article retiré du panier.']);
    }

    #[Route('/panier/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(CartRepository $cartRepository): JsonResponse {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return new JsonResponse(['error' => 'Vous devez être connecté.'], 403);
        }

        $cart = $cartRepository->findOrCreateCart($client);
        $cartRepository->clearCart($cart);

        return new JsonResponse(['success' => 'Panier vidé.']);
    }
}