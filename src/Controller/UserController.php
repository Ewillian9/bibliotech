<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'user_')]
class UserController extends AbstractController
{
    /**
     * Liste tous les utilisateurs.
     * @Route("/", name="list", methods={"GET"})
     */
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->findAll());
    }

    /**
     * Récupère un utilisateur spécifique par son ID.
     * @Route("/{id}", name="show", methods={"GET"})
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user);
    }

    /**
     * Crée un nouvel utilisateur.
     * @Route("/create", name="create", methods={"POST"})
     */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupération et décodage des données envoyées en JSON
        $data = json_decode($request->getContent(), true);

        // Création de l'utilisateur
        $user = new User();
        $user->setName($data['name'])
             ->setEmail($data['email'])
             ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)) // Hash du mot de passe
             ->setRole($data['role']);

        // Persistance et enregistrement en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur créé avec succès'], 201);
    }

    /**
     * Supprime un utilisateur par son ID.
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur supprimé']);
    }
}
