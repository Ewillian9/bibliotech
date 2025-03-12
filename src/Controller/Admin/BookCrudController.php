<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Book')
                ->setIcon('fa fa-book')
                ->setHelp('Panneau contenant des infos de base.'),
            TextField::new('title')
                ->setHelp("Le titre du livre."),
            TextField::new('author')
                ->setHelp("L'auteur du livre."),
            TextField::new('genre')
                ->setHelp("Le genre du livre."),
            TextField::new('image')
                ->setHelp("Lien vers l'image de couverture du livre."),
            TextField::new('overviw')
                ->setHelp("Résumé du livre."),
        ];
    }
}
