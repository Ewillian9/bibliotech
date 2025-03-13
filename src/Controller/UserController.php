<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\LoanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Retourne le profil d'un utilisateur en JSON.
     * URL : GET /{id}/profile
     */
    #[Route('/{id}/profile', name: 'user_profile_json', methods: ['GET'])]
    public function profile(User $user): JsonResponse
    {
        return $this->json([
            'id'    => $user->getId(),
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
            'role'  => $user->getRole(),
        ]);
    }

    /**
     * Affiche le profil de l'utilisateur via Twig.
     * URL : GET /{id}/profile/view
     */
    #[Route('/{id}/profile/view', name: 'user_profile_view', methods: ['GET'])]
    public function profileView(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Retourne l'historique des emprunts en JSON.
     * URL : GET /{id}/loans
     */
    #[Route('/{id}/loans', name: 'loan_history_json', methods: ['GET'])]
    public function loanHistory(User $user, LoanRepository $loanRepository): JsonResponse
    {
        $loans = $loanRepository->findBy(['client' => $user]);
        return $this->json($loans);
    }

    /**
     * Affiche l'historique des emprunts via Twig.
     * URL : GET /{id}/loans/view
     */
    #[Route('/{id}/loans/view', name: 'user_loans_view', methods: ['GET'])]
    public function loanHistoryView(User $user, LoanRepository $loanRepository): Response
    {
        $loans = $loanRepository->findBy(['client' => $user]);
        return $this->render('user/loan_history.html.twig', [
            'user'  => $user,
            'loans' => $loans,
        ]);
    }
}

