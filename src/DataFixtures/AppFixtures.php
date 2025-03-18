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

        // 📌 Définition des catégories
        $categoriesData = [
            'Littérature', 'Science-Fiction', 'Histoire', 'Philosophie',
            'Art', 'Science', 'Informatique', 'Économie', 'Psychologie'
        ];

        // 📌 Vérifier si les catégories existent déjà, sinon les créer
        $categories = [];
        foreach ($categoriesData as $catName) {
            $category = $manager->getRepository(Category::class)->findOneBy(['catName' => $catName]);
            if (!$category) {
                $category = new Category();
                $category->setCatName($catName);
                $manager->persist($category);
            }
            $categories[] = $category;
        }

        // 📌 Récupération des livres déjà importés
        $books = $manager->getRepository(Book::class)->findAll();
        if (empty($books)) {
            throw new \Exception("Aucun livre trouvé en base. Assurez-vous d'avoir importé les livres via l'API Google Books.");
        }

        // 📌 Assignation aléatoire des livres à une catégorie si ce n'est pas encore fait
        foreach ($books as $book) {
            if (!$book->getCategory()) {
                $book->setCategory($faker->randomElement($categories));
                $manager->persist($book);
            }
        }

        // 📌 Création des utilisateurs
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

        // 📌 Création d'un administrateur
        $admin = new User();
        $admin->setName('Admin User')
            ->setEmail('admin@example.com')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'))
            ->setRole('ROLE_ADMIN');

        $manager->persist($admin);

        // 📌 Création des emprunts (5 par utilisateur)
        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $loan = new Loan();
                $loan->setBook($faker->randomElement($books))
                     ->setClient($user)
                     ->setStatus($faker->randomElement(['En cours', 'Terminé', 'En retard']))
                     ->setCreatedAt(new \DateTimeImmutable())
                     ->setReturnedAt((new \DateTimeImmutable())->modify('+30 days'));

                $manager->persist($loan);
            }
        }

        // 🚀 Sauvegarde en base
        $manager->flush();
    }
}
