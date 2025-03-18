<?php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Category;
use App\Service\GoogleBooksService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-books')]
class ImportBooksCommand extends Command
{
    private GoogleBooksService $googleBooksService;
    private EntityManagerInterface $entityManager;

    public function __construct(GoogleBooksService $googleBooksService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->googleBooksService = $googleBooksService;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $booksData = $this->googleBooksService->searchBooks('programming', 10);

        foreach ($booksData as $bookData) {
            $book = new Book();
            
            $book->setTitle($bookData['title'] ?? 'Titre inconnu')
                 ->setAuthors(isset($bookData['authors']) ? implode(', ', $bookData['authors']) : 'Auteur inconnu')
                 ->setGenre($bookData['genre'] ?? 'Inconnu')
                 ->setIsAvailable(true)
                 ->setOverview($bookData['description'] ?? 'Pas de description disponible')
                 ->setRating($bookData['rating'] ?? 0)
                 ->setImage($bookData['thumbnail'] ?? 'default.jpg');

            // Gestion des catégories
            $categoryName = $bookData['genre'] ?? 'Inconnu';
            $category = $this->entityManager->getRepository(Category::class)->findOneBy(['catName' => $categoryName]);

            if (!$category) {
                $category = new Category();
                $category->setCatName($categoryName);
                $this->entityManager->persist($category);
                $this->entityManager->flush();
            }

            $book->setCategory($category);

            $this->entityManager->persist($book);
        }

        // Une seule exécution du flush après la boucle
        $this->entityManager->flush();

        $output->writeln('Livres importés avec succès depuis Google Books API.');

        return Command::SUCCESS;
    }
}
