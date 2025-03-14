<?php

namespace App\Controller\Admin;

use App\Entity\Loan;
use Dom\Text;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            TextField::new('book.title', 'Titre du livre')
                ->setHelp("Le titre du livre.")
                ->setFormTypeOption('choice_label', 'title'),

            // Afficher le nom de l'utilisateur (relier à User)
            TextField::new('client.name', 'Nom de l\'utilisateur')
                ->setHelp("L'utilisateur qui a emprunté le livre.")
                ->setFormTypeOption('choice_label', 'name'),

            // Afficher la date de création de l'emprunt
            DateTimeField::new('created_at')
                ->setFormat('dd/MM/yyyy HH:mm') 
                ->setHelp("Date de création de l'emprunt."),

            // Afficher la date de retour du livre
            DateTimeField::new('returned_at')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->setHelp("Date de retour du livre."),

            // Afficher le statut de l'emprunt
            TextField::new('status')
                ->setHelp("Statut de l'emprunt."),
        ];
    }
}

