<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Loan;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Définition des rôles possibles
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];

        // Définition des catégories
        $categoriesData = [
            'Littérature', 'Science-Fiction', 'Histoire', 'Philosophie', 
            'Art', 'Science', 'Informatique', 'Économie', 'Psychologie'
        ];

        // Définition des genres littéraires
        $genresData = [
            'Roman', 'Essai', 'Biographie', 'Poésie', 'Nouvelle', 
            'Fantastique', 'Thriller', 'Science-Fiction', 'Manga'
        ];

        // Création des catégories
        $categories = [];
        foreach ($categoriesData as $catName) {
            $category = new Category();
            $category->setCatName($catName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Création des utilisateurs
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName($faker->name)
                ->setEmail($faker->unique()->safeEmail)
                ->setPassword($this->passwordHasher->hashPassword($user, 'password123'))
                ->setRole('ROLE_USER');

            $manager->persist($user);
            $users[] = $user;
        }

        // Création d'un administrateur
        $admin = new User();
        $admin->setName('Admin User')
            ->setEmail('admin@example.com')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'))
            ->setRole('ROLE_ADMIN');

        $manager->persist($admin);

        // Création des livres
        $books = [];
        for ($i = 0; $i < 1000; $i++) {
            $book = new Book();
            $book->setTitle($faker->sentence(3))
                ->setAuthor($faker->name)
                ->setGenre($faker->randomElement($genresData))
                ->setIsAvailable($faker->boolean(80)) // 80% des livres sont disponibles
                ->setCategory($faker->randomElement($categories))
                ->setRating($faker->randomFloat(1, 1, 5))
                ->setOverview($faker->paragraph(3))
                ->setImage($faker->imageUrl(200, 300, 'books'));

            $manager->persist($book);
            $books[] = $book;
        }

        // Création des emprunts (20 par utilisateur)
        foreach ($users as $user) {
            for ($i = 0; $i < 20; $i++) {
                $loan = new Loan();
                $loan->setBook($faker->randomElement($books))
                     ->setClient($user)
                     ->setStatus($faker->randomElement(['En cours', 'Terminé', 'En retard']))
                     ->setCreatedAt(new \DateTimeImmutable())
                     ->setReturnedAt((new \DateTimeImmutable())->modify('+30 days'));

                $manager->persist($loan);
            }
        }

        $manager->flush();
    }
}