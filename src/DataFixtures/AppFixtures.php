<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        // $genre = ['roman', 'policier', 'fantastique', 'science-fiction', 'biographie', 'théâtre', 'poésie', 'nouvelle', 'essai', 'document', 'bd', 'manga', 'comics', 'autre'];
        // $categoryNames = ['adulte', 'adolescent', 'enfant', 'autre']; // Renommé
        // $status = ['en cours', 'terminé', 'annulé'];

        // Création de l'Admin 
        $admin = new User();
        $admin
            ->setEmail('admin@admin.com')
            ->setName('admin')
            ->setPassword('admin123')
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($admin);

        // Création des Utilisateurs
        $users = [];
        for ($i = 0; $i < 10; $i++) { 
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setName($faker->name)
                ->setPassword('password123')
                ->setRoles(['ROLE_USER'])
            ;
            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
