<?php

namespace App\Controller;

use App\Entity\LigneFacture;
use App\Enum\StatusEnum;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Client;
use App\Entity\Facture;
use App\Form\AdresseLivraisonType;
use App\Form\ClientType;
use App\Repository\ArticleRepository;
use App\Repository\CartRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'app_cart')]
    public function index(CartRepository $cartRepository): Response
    {
        $client = $this->getUser();

        if (!$client instanceof Client) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre panier.');
        }

        $cart = $cartRepository->findOrCreateCart($client);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'items' => $cart->getCartItems(),
        ]);
    }

    #[Route('/panier/validation', name: 'app_cart_validation')]
    public function validation(Request $request, CartRepository $cartRepository, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre panier.');
        }

        $cart = $cartRepository->findOrCreateCart($client);
        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $formAdresse = $this->createForm(AdresseLivraisonType::class, null, [
            'data' => [
                'adresse' => $client->getAdresse(),
                'complement_adr' => $client->getComplementAdr(),
                'CP' => $client->getCP(),
                'ville' => $client->getVille(),
                'pays' => $client->getPays(),
            ]
        ]);

        $formAdresse->handleRequest($request);

        if ($formAdresse->isSubmitted() && $formAdresse->isValid()) {
            $total = 0;
            foreach ($cart->getCartItems() as $item) {
                $total += $item->getArticle()->getPrice() * $item->getQuantity();
            }
            $adresseLivraison = sprintf("%s, %s, %s %s, %s",
                $formAdresse->get('adresse')->getData(),
                $formAdresse->get('complement_adr')->getData(),
                $formAdresse->get('CP')->getData(),
                $formAdresse->get('ville')->getData(),
                $formAdresse->get('pays')->getData()
            );

            $facture = new Facture();
            $facture->setIdClient($client);
            $facture->setDateFacture(new DateTimeImmutable());
            $facture->setAdresseLivraison($adresseLivraison);
            $facture->setStatut(StatusEnum::EN_COURS);
            $facture->setTotal($total);

            foreach ($cart->getCartItems() as $cartItem) {
                $ligneFacture = new LigneFacture();
                $ligneFacture->setIdFacture($facture);
                $ligneFacture->setIdArticle($cartItem->getArticle());
                $ligneFacture->setQuantity($cartItem->getQuantity());
                $ligneFacture->setUnitPrice($cartItem->getArticle()->getPrice());
                $ligneFacture->setTotalLigne($cartItem->getQuantity() * $cartItem->getArticle()->getPrice());
                $entityManager->persist($ligneFacture);

                $total += $ligneFacture->getTotalLigne();
            }

            $entityManager->persist($facture);
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse de livraison a été mise à jour.');

            $cartRepository->clearCart($cart);
            // Redirection vers la page de résumé des commandes
            return $this->redirectToRoute('app_facture');
        }

        return $this->render('cart/validation.html.twig', [
            'cart' => $cart,
            'formAdresse' => $formAdresse->createView(),
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

        $variant = $request->get('variant');
        $variant = $variant === '' ? null : $variant;
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

    #[Route('/panier/increment/{id}', name: 'app_cart_increment', methods: ['POST'])]
    public function increment(int $id, Request $request, CartRepository $cartRepository, ArticleRepository $articleRepository): JsonResponse {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return new JsonResponse(['error' => 'Vous devez être connecté.'], 403);
        }

        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article non trouvé.'], 404);
        }

        $variant = $request->get('variant');
        $variant = $variant === '' ? null : $variant;

        $cart = $cartRepository->findOrCreateCart($client);
        $cartRepository->incrementCartItemQuantity($cart, $article, $variant);

        return new JsonResponse(['success' => 'Quantité augmentée.']);
    }

    #[Route('/panier/decrement/{id}', name: 'app_cart_decrement', methods: ['POST'])]
    public function decrement(int $id, Request $request, CartRepository $cartRepository, ArticleRepository $articleRepository): JsonResponse {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return new JsonResponse(['error' => 'Vous devez être connecté.'], 403);
        }

        $article = $articleRepository->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article non trouvé.'], 404);
        }

        $variant = $request->get('variant');
        $variant = $variant === '' ? null : $variant;

        $cart = $cartRepository->findOrCreateCart($client);
        $cartRepository->decrementCartItemQuantity($cart, $article, $variant);

        return new JsonResponse(['success' => 'Quantité diminuée.']);
    }

}
