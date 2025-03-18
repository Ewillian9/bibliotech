<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoanController extends AbstractController
{
    #[Route('/loan/return/{id}', name: 'loan_return', methods: ['GET'])]
    public function returnLoan(int $id, LoanRepository $loanRepo, EntityManagerInterface $em): Response
    {
        // Récupérer le prêt
        $loan = $loanRepo->find($id);
        if (!$loan) {
            throw $this->createNotFoundException("Emprunt introuvable");
        }
        
        // Mettre à jour le statut, par ex. en "Terminé"
        $loan->setStatus("Terminé");
        $em->flush();
        
        // Message flash
        $this->addFlash('success', 'Livre restitué avec succès !');
        
        // Rediriger vers la page de profil (ou autre)
        return $this->redirectToRoute('user_profile_view', [
            'id' => $loan->getClient()->getId()
        ]);
    }
}
