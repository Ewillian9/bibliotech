<?php

namespace App\Controller\Admin;

use App\Entity\Loan;
use Dom\Text;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class LoanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Loan::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Loan')
                ->setIcon('fa fa-book')
                ->setHelp('Panneau contenant des infos de base.'),

            // Afficher le titre du livre (relier à Book)
            // afficher le titre du livre dans le formulaire d'emprunt
            TextField::new('book.title', 'Titre du livre')
                ->setHelp("Le titre du livre."),

            // Afficher le nom de l'utilisateur (relier à User)
            // afficher le nom de l'utilisateur dans le formulaire d'emprunt
            TextField::new('client.name', 'Nom de l\'utilisateur')
                ->setHelp("L'utilisateur qui a emprunté le livre."),

            // Afficher la date de création de l'emprunt
            DateTimeField::new('created_at')
                ->setFormat('dd/MM/yyyy HH:mm') 
                ->setHelp("Date de création de l'emprunt."),

            // Afficher la date de retour du livre
            DateTimeField::new('returned_at')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->setHelp("Date de retour du livre."),

            // Afficher le statut de l'emprunt
            ChoiceField::new('status')
                ->setChoices(['En cours' => 'En cours',
                 'Terminé' => 'Terminé',
                  'En retard' => 'En retard'])
                ->setHelp("Statut de l'emprunt."),
                
        ];
    }
}

