<?php

namespace App\Controller\Admin;

use App\Entity\Facture;
use App\Enum\StatusEnum;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class FactureCrudController extends AbstractCrudController
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Facture) {
            // Vérifier le changement de statut
            $originalStatus = $entityManager
                ->getUnitOfWork()
                ->getOriginalEntityData($entityInstance)['statut'] ?? null;

            if ($originalStatus !== $entityInstance->getStatut()) {
                // Envoi de l'email
                $this->mailerService->sendOrderStatusUpdate(
                    $entityInstance->getIdClient()->getMail(),
                    'Mise à jour de votre commande',
                    'mails/order_status_update.html.twig',
                    [
                        'client' => $entityInstance->getIdClient(),
                        'order' => $entityInstance,
                    ]
                );
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public static function getEntityFqcn(): string
    {
        return Facture::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $statusField = ChoiceField::new('statut', 'Statut')
            ->setChoices(StatusEnum::getChoices())
            ->renderAsNativeWidget()
            ->setFormTypeOption('choice_label', fn (StatusEnum $choice) => $choice->getLabel());

        if ($pageName === 'index') {
            // Affichage en texte seulement dans l'index
            $statusField = $statusField->setFormTypeOption('disabled', true)->setTextAlign('center');
        }

        return [
            IdField::new('id')->onlyOnIndex(),

            AssociationField::new('id_client', 'Client')
                ->setCrudController(ClientCrudController::class),

            CollectionField::new('ligneFactures', 'Articles de la commande')
                ->setTemplatePath('admin/fields/ligne_factures.html.twig')
                ->onlyOnIndex(),

            TextField::new('adresseLivraison', 'Adresse de livraison'),

            DateField::new('date_facture')->onlyOnIndex(),

            DateTimeField::new('updated_at', 'Dernière mise à jour')
                ->setFormTypeOption('disabled', true)
                ->onlyOnDetail(),

            MoneyField::new('total', 'Total')
                ->setCurrency('EUR')
                ->setStoredAsCents(false),
            $statusField,
        ];
    }
}
