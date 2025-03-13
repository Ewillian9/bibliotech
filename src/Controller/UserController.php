<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * Inscription d'un nouvel utilisateur.
     * Route : POST /api/users/register
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name'])
             ->setEmail($data['email'])
             ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT))
             ->setRole('ROLE_USER'); // Rôle par défaut pour les utilisateurs non admin

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'Utilisateur enregistré avec succès',
            'userId' => $user->getId()
        ], 201);
    }

    /**
     * Connexion d'un utilisateur.
     * Route : POST /api/users/login
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Recherche de l'utilisateur par email
        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['error' => 'Email invalide'], 401);
        }

        // Vérification du mot de passe
        if (!password_verify($password, $user->getPassword())) {
            return $this->json(['error' => 'Mot de passe incorrect'], 401);
        }

        // Pour un projet réel, on renverrait ici un token d'authentification (ex: JWT)
        return $this->json([
            'message' => 'Connexion réussie',
            'user' => [
                'id'   => $user->getId(),
                'name' => $user->getName(),
                'email'=> $user->getEmail(),
                'role' => $user->getRole()
            ]
        ]);
    }

    /**
     * Consultation du profil d'un utilisateur en JSON.
     * Route : GET /api/users/{id}/profile
     */
    #[Route('/{id}/profile', name: 'profile', methods: ['GET'])]
    public function profile(User $user): JsonResponse
    {
        return $this->json([
            'id'    => $user->getId(),
            'name'  => $user->getName(),
            'email' => $user->getEmail(),
            'role'  => $user->getRole()
        ]);
    }

    /**
     * Affichage du profil utilisateur via Twig.
     * Route : GET /api/users/{id}/profile/view
     */
    #[Route('/{id}/profile/view', name: 'profile_view', methods: ['GET'])]
    public function profileView(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Récupération de l'historique des emprunts en JSON.
     * Route : GET /api/users/{id}/loans
     */
    #[Route('/{id}/loans', name: 'loan_history', methods: ['GET'])]
    public function loanHistory(User $user, LoanRepository $loanRepository): JsonResponse
    {
        $loans = $loanRepository->findBy(['client' => $user]);
        return $this->json($loans);
    }

    /**
     * Affichage de l'historique des emprunts via Twig.
     * Route : GET /api/users/{id}/loans/view
     */
    #[Route('/{id}/loans/view', name: 'loans_view', methods: ['GET'])]
    public function loanHistoryView(User $user, LoanRepository $loanRepository): Response
    {
        $loans = $loanRepository->findBy(['client' => $user]);
        return $this->render('user/loan_history.html.twig', [
            'user'  => $user,
            'loans' => $loans
        ]);
    }
}
