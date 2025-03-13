<?php

namespace App\Controller\Admin;

use App\Entity\Loan;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Text;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class LoanCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public static function getEntityFqcn(): string
    {
        return Loan::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $acceptAction = Action::new('accept', '✅ Accepter', 'fa fa-check')
            ->linkToCrudAction('acceptLoan')
            ->setCssClass('btn btn-success')
            ->displayIf(function ($loan) {
                return $loan->getAction() === 'En attente';
            });

        $refuseAction = Action::new('refuse', '❌ Refuser', 'fa fa-times')
            ->linkToCrudAction('refuseLoan')
            ->setCssClass('btn btn-danger')
            ->displayIf(function ($loan) {
                return $loan->getAction() === 'En attente';
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $acceptAction)
            ->add(Crud::PAGE_INDEX, $refuseAction)
            ->disable(Action::NEW, Action::EDIT); // Désactive les actions non nécessaires
    }

    public function acceptLoan(AdminContext $context): RedirectResponse
    {
        $loan = $context->getEntity()->getInstance();
        
        if ($loan->getAction() === 'En attente') {
            $loan->setAction('Accepté');
            $loan->getBook()->setIsAvailable(false);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Emprunt accepté avec succès');
        }

        return $this->redirect($context->getReferrer());
    }

    public function refuseLoan(AdminContext $context): RedirectResponse
    {
        $loan = $context->getEntity()->getInstance();
        
        if ($loan->getAction() === 'En attente') {
            $loan->setAction('Refusé');
            $this->entityManager->flush();
            
            $this->addFlash('error', 'Emprunt refusé avec succès');
        }

        return $this->redirect($context->getReferrer());
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Informations sur l\'emprunt')
                ->setIcon('fa fa-info')
                ->setHelp('Panneau contenant des infos de base.'),

            TextField::new('book.title', 'Titre du livre')
                ->setHelp("Le titre du livre.")
                ->setFormTypeOption('choice_label', 'title'),
            TextField::new('client.name', 'Nom de l\'emprunteur')
                ->setHelp("Le nom de l'emprunteur.")
                ->setFormTypeOption('choice_label', 'name'),
            DateTimeField::new('createdAt', 'Date de demande')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->setHelp("La date de demande d'emprunt."),
            DateTimeField::new('returnedAt', 'Date de retour')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->setHelp("La date de retour du livre."),
            TextField::new('status', 'Statut')
                ->setHelp("Le statut de l'emprunt."),
            ChoiceField::new('action', 'Action')
                ->setChoices([
                    'En attente' => 'En attente',
                    'Accepté' => 'Accepté',
                    'Refusé' => 'Refusé'
                ])
                ->renderAsBadges([
                    'En attente' => 'warning',
                    'Accepté' => 'success',
                    'Refusé' => 'danger'
                ])
        ];
    }
}
