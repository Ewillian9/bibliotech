<?php

namespace App\Controller\Admin;

use App\Entity\Loan;
use Symfony\Component\DomCrawler\Form;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            AssociationField::new('book')
                ->setHelp("Le titre du livre.")
                ->setFormTypeOption('choice_label', 'title'), 
            AssociationField::new('client')
                ->setHelp("L'utilisateur qui a emprunté le livre.")
                ->setFormTypeOption('choice_label', 'name'),
            TextField::new('creationDate')
                ->setHelp("Date de création de l'emprunt."),
            TextField::new('returnDate')
                ->setHelp("Date de retour du livre."),
            TextField::new('status')
                ->setHelp("Statut de l'emprunt."),
        ];
    }
    
}
