<?php

namespace App\Controller;

use App\Enum\StatusEnum;
use App\Entity\Client;
use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FactureController extends AbstractController
{
    // Route pour les commandes en cours dans le compte client
    #[Route('/myorders', name: 'app_facture')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        if (!$client) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos commandes.');
        }

        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $orders = $entityManager->getRepository(Facture::class)
            ->findBy(['id_client' => $client->getId(), 'statut' => StatusEnum::EN_COURS], ['date_facture' => 'DESC']);

        return $this->render('facture/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    // Route pour les commandes passées dans le compte client
    #[Route('/mypastorders', name: 'app_facture_past')]
    public function pastIndex(EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        if (!$client) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos commandes.');
        }

        $orders = $entityManager->getRepository(Facture::class)
            ->findBy(['id_client' => $client->getId(), 'statut' => 'terminée'], ['date_facture' => 'DESC']);

        return $this->render('facture/pastorders.html.twig', [
            'orders' => $orders,
        ]);
    }

    // Route pour la creation d'une commande
    #[Route('/createorder', name: 'app_facture_create')]
    public function createOrder(CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        if (!$client) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour valider une commande.');
        }

        // Récupération du panier de l'utilisateur
        $cart = $cartRepository->findOneBy(['client' => $client]);
        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        // Création de la facture
        $facture = new Facture();
        $facture->setIdClient($client);
        $facture->setDateFacture(new \DateTimeImmutable());
        $facture->setStatut(StatusEnum::EN_COURS);
        $total = 0;

        // Ajout des lignes de facture
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

        $facture->setTotal($total);

        // Sauvegarde de la commande
        $entityManager->persist($facture);
        $entityManager->flush();

        // Vider le panier après validation
        $cartRepository->clearCart($cart);

        $this->addFlash('success', 'Votre commande a été validée.');
        return $this->redirectToRoute('app_facture');
    }
}
