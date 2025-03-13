<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BookController extends AbstractController
{

    // route pour afficher la detail d'un livre
    #[Route('/book/{id}', name: 'book_show', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    // route pour emprunter un livre
    #[Route('/book/{id}/loan', name: 'book_loan')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function loan(Book $book, EntityManagerInterface $entityManager, LoanRepository $loanRepository): RedirectResponse
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur a déjà emprunté 5 livres
        $existingLoans = $loanRepository->count(['client' => $user, 'status' => 'En attente']);
        if ($existingLoans >= 5) {
            $this->addFlash('error', 'Vous ne pouvez pas emprunter plus de 5 livres en même temps.');
            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        // Vérifier si le livre est déjà emprunté
        if (!$book->isAvailable()) {
            $this->addFlash('error', 'Ce livre n\'est pas disponible.');
            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        // Créer une nouvelle demande d'emprunt
        $loan = new Loan();
        $loan->setBook($book);
        $loan->setClient($user);
        $loan->setAction('En attente');

        $entityManager->persist($loan);
        $entityManager->flush();

        $this->addFlash('success', 'Votre demande d\'emprunt a été envoyée à l\'administrateur.');

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

    #[Route('/user/loans', name: 'user_loans', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function userLoans(LoanRepository $loanRepository): Response
    {
        $user = $this->getUser();
        $loans = $loanRepository->findBy(['client' => $user]);

        return $this->render('book/user_loans.html.twig', [
            'loans' => $loans,
        ]);
    }


    #[Route('/admin/loans', name: 'admin_loans', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminLoans(LoanRepository $loanRepository): Response
    {
        // Récupérer uniquement les demandes en attente
        $loans = $loanRepository->findBy(['status' => 'En attente']);
        
        return $this->render('book/admin_loans.html.twig', [
            'loans' => $loans,
        ]);
    }

    #[Route('/admin/loan/{id}/accept', name: 'admin_loan_accept', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function acceptLoan(Loan $loan, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($loan->getStatus() === 'En attente') {
            $loan->setStatus('Accepté');
            $loan->setAction('Terminé');
            $loan->getBook()->setIsAvailable(false);
            // $loan->setApprovedAt(new \DateTime());
            $entityManager->flush();
            
            // Ici vous pouvez ajouter une notification par email
            $this->addFlash('success', 'Demande acceptée avec succès');
        }
        return $this->redirectToRoute('admin_loans');
    }

    #[Route('/admin/loan/{id}/refuse', name: 'admin_loan_refuse', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function refuseLoan(Loan $loan, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($loan->getStatus() === 'En attente') {
            $loan->setStatus('Refusé');
            $loan->setAction('Annulé');
            $entityManager->flush();
            
            $this->addFlash('error', 'Demande refusée avec succès');
        }
        return $this->redirectToRoute('admin_loans');
    }
}