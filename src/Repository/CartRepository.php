<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    // Créer un panier si le client n'en n'a pas deja un
    public function findOrCreateCart(Client $client): Cart {
        $cart = $this->findOneBy(['client' => $client]);

        if(!$cart) {
            $cart = new Cart();
            $cart->setClient($client);
            $this->getEntityManager()->persist($cart);
            $this->getEntityManager()->flush();
        }
        return $cart;
    }

    // Ajoute un item au panier
    public function addItemToCart(Cart $cart, Article $article, int $quantity, ?string $variant) {
        $em = $this->getEntityManager();

        foreach ($cart->getCartItems() as $cartItem) {
            if($cartItem->getArticle()->getId() === $article->getId() && $cartItem->getVariant() === $variant) {
                $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
                $em->persist($cartItem);
                $em->flush();
                return;
            }
        }

        $cartItem = new CartItem();
        $cartItem->setCart($cart);
        $cartItem->setArticle($article);
        $cartItem->setQuantity($quantity);
        $cartItem->setPrice($article->getPrice());
        $cartItem->setVariant($variant);

        $cart->addCartItem($cartItem);
        $em->persist($cartItem);
        $em->persist($cart);
        $em->flush();
    }

    // Supprime un item du panier
    public function removeItemFromCart(Cart $cart, Article $article, ?string $variant) {
        $em = $this->getEntityManager();
        foreach ($cart->getCartItems() as $cartItem) {
            if (
                $cartItem->getArticle()->getId() === $article->getId() &&
                ($cartItem->getVariant() === $variant || ($cartItem->getVariant() === null && $variant === null))
            ) {
                $cart->removeCartItem($cartItem);
                $em->remove($cartItem);
                $em->flush();
                return;
            }
        }
    }

    public function clearCart(Cart $cart) {
        $em = $this->getEntityManager();

        foreach ($cart->getCartItems() as $cartItem) {
            $cart->removeCartItem($cartItem);
            $em->remove($cartItem);
        }
        $em->flush();
    }

    public function getCartItemsCount(Cart $cart): int {
        $count = 0;
        foreach ($cart->getCartItems() as $item) {
            $count += $item->getQuantity();
        }
        return $count;
    }
}
