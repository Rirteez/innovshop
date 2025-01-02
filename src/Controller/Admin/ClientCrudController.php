<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            EmailField::new('mail', 'Email'),
            TextField::new('adresse', 'Adresse')->onlyOnDetail(),
            TextField::new('complement_adr', 'Complément d\'adresse')->onlyOnDetail(),
            TextField::new('CP', 'Code Postal')->onlyOnDetail(),
            TextField::new('ville', 'Ville')->onlyOnDetail(),
            TextField::new('pays', 'Pays')->onlyOnDetail(),
            ArrayField::new('roles', 'Rôles'),
            AssociationField::new('factures', 'Commandes associées')
                ->formatValue(function ($value, $entity) {
                    $factures = $entity->getFactures();
                    return $factures->count() . ' commande(s)';
                })
                ->onlyOnIndex()
        ];
    }

}
