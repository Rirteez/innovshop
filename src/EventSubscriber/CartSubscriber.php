<?php

namespace App\EventSubscriber;

use App\Service\CartService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CartSubscriber implements EventSubscriberInterface {
    private $cartService;
    private $twig;

    public function __construct(CartService $cartService, Environment $twig) {
        $this->cartService = $cartService;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event) {
        $cartItemCount = $this->cartService->getCartItemCount();
        $this->twig->addGlobal('cartItemCount', $cartItemCount);
    }
}