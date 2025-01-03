<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Facture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('InnovShop');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Gestion des articles', 'fas fa-boxes-packing', Article::class);
        yield MenuItem::linkToCrud('Gestion des categories', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Gestion des clients', 'fas fa-user', Client::class);
        yield MenuItem::linkToCrud('Gestion des commandes', 'fas fa-shopping-cart', Facture::class);
    }
}
