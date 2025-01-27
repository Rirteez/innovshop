<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Doctrine\ORM\EntityManagerInterface;


class ArticleCrudController extends AbstractCrudController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('title'),
            TextEditorField::new('description'),
            NumberField::new('price'),
            TextEditorField::new('description_detail'),
            ChoiceField::new('colors')
                ->setChoices(array_flip(Article::VARIANTS))
                ->allowMultipleChoices()
                ->renderExpanded(false),
            ImageField::new('images')
                ->setBasePath('/images/uploads/')
                ->setUploadDir('public/images/uploads/')
                ->setRequired(false)
                ->setFormTypeOption('multiple',true),
            BooleanField::new('flash_or_no'),

            AssociationField::new('categories')->autocomplete()
        ];

    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;

        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitle())->lower());

        parent::persistEntity($entityManager, $entityInstance);
    }

}
