<?php

namespace App\Service;

use App\Entity\Client;
use App\Repository\CartRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CartService {
    private $cartRepository;
    private $security;

    public function __construct(CartRepository $cartRepository, Security $security)
    {
        $this->cartRepository = $cartRepository;
        $this->security = $security;
    }

    public function getCartItemCount(): int {
        $user = $this->security->getUser();

        if ($user instanceof Client) {
            $cart = $this->cartRepository->findOneBy(['client' => $user]);
            if ($cart){
                return $this->cartRepository->getCartItemsCount($cart);
            }
        }

        return 0;
    }
}