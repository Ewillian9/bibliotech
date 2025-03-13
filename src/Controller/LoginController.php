<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
   /**
     * Affiche le formulaire de connexion.
     * URL : GET /login
     */
    #[Route('/login', name: 'login_form', methods: ['GET'])]
    public function loginForm(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * Traitement de la connexion d'un utilisateur et redirection vers le profil.
     * URL : POST /login
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): Response
    {
        // Tente de décoder le contenu en JSON ; sinon, utilise les paramètres du formulaire
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            $data = $request->request->all();
        }

        // Les formulaires Symfony utilisent généralement _username et _password
        $email = $data['email'] ?? $data['_username'] ?? '';
        $password = $data['password'] ?? $data['_password'] ?? '';

        if (empty($email) || empty($password)) {
            return new Response("Email et mot de passe requis", 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return new Response("Email invalide", 401);
        }

        if (!password_verify($password, $user->getPassword())) {
            return new Response("Mot de passe incorrect", 401);
        }

        // Stocke l'ID utilisateur dans la session (simule la connexion)
        $session = $request->getSession();
        $session->set('user_id', $user->getId());

        // Redirige vers la page de profil de l'utilisateur.
        // Assurez-vous que la route 'profile_view' existe et qu'elle prend l'ID en paramètre.
        return $this->redirectToRoute('profile_view', ['id' => $user->getId()]);
    }

    /**
     * Déconnexion de l'utilisateur.
     * URL : GET /logout
     */
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();
        // Redirige vers le formulaire de connexion après déconnexion.
        return $this->redirectToRoute('login_form');
    }
}


