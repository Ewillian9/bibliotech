<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    /**
     * Affiche le formulaire d'inscription.
     * URL : GET /register
     */
    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(): Response
    {
        return $this->render('registration/register.html.twig');
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur.
     * URL : POST /register
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Récupère les données du formulaire
        $data = $request->request->all();
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;

        // Vérification des champs obligatoires
        if (!$name || !$email || !$plainPassword) {
            $this->addFlash('error', 'Tous les champs sont requis.');
            return $this->redirectToRoute('register_form');
        }

        // Vérifier si l'email est déjà utilisé
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return $this->redirectToRoute('register_form');
        }

        // Création de l'utilisateur
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setRole('ROLE_USER');

        // Hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Persistance en base
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');

        // Redirige vers la page de connexion (assurez-vous que la route login_form existe)
        return $this->redirectToRoute('login_form');
    }
}
