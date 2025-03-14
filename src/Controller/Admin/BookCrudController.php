<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Validator\Constraints\Image;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
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

            // Afficher le titre du livre
            TextField::new('title')
                ->setHelp("Le titre du livre."),
            
            // Afficher l'auteur du livre
            TextField::new('authors')
                ->setHelp("L'auteur du livre."),

            // Afficher le genre du livre
            TextField::new('genre')
                ->setHelp("Le genre du livre."),

            // Afficher l'image de couverture du livre
            ImageField::new('image')
                ->setHelp("Image de couverture du livre.")
                ->setBasePath('media/images')
                ->setUploadDir('public/media/images')
                ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
                ->setFileConstraints(new Image(maxSize: '10M')),

            // Afficher le résumé du livre
            TextField::new('overview')
                ->setHelp("Résumé du livre."),
        ];
    }
}
