<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\LoanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    /**
     * Affiche le profil de l'utilisateur via Twig.
     * URL : GET /user/{id}/profile
     */
    #[Route('/user/{id}/profile', name: 'user_profile_view', methods: ['GET'])]
    public function profileView(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Affiche l'historique des emprunts de l'utilisateur via Twig.
     * URL : GET /user/{id}/loans
     */
    #[Route('/user/{id}/loans', name: 'user_loans_view', methods: ['GET'])]
    public function loanHistoryView(User $user, LoanRepository $loanRepository): Response
    {
        $loans = $loanRepository->findBy(['client' => $user]);
        return $this->render('user/loan_history.html.twig', [
            'user'  => $user,
            'loans' => $loans,
        ]);
    }
}
