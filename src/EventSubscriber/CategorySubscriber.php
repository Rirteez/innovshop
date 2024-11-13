<?php

namespace App\EventSubscriber;

use App\Service\CategoryService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CategorySubscriber implements EventSubscriberInterface {
    private CategoryService $categoryService;
    private Environment $twig;

    public function __construct(CategoryService $categoryService, Environment $twig) {
        $this->categoryService = $categoryService;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents():array {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event):void {
        $categories = $this->categoryService->getAllCategories();
        $totalCategories = count($categories);

        $this->twig->addGlobal('categories', $categories);
        $this->twig->addGlobal('totalCategories', $totalCategories);
    }
}

?>