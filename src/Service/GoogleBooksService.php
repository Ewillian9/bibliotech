<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BookRepository;
use App\Entity\Category;
use App\Entity\Book;

class GoogleBooksService
{
    private HttpClientInterface $client;
    private ?string $apiKey;
    private EntityManagerInterface $entityManager;
        private BookRepository $bookRepository;

    public function __construct(HttpClientInterface $client, string $apiKey = null, EntityManagerInterface $entityManager, BookRepository $bookRepository)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
    }

    // Récupère les livres depuis l'API Google Books
    public function searchBooks(string $query, int $maxResults = 30): array
    {
        $url = 'https://www.googleapis.com/books/v1/volumes';

        $params = [
            'q' => $query,
            'maxResults' => $maxResults,
            'key' => $this->apiKey
        ];

        try {
            $response = $this->client->request('GET', $url, ['query' => $params]);
            $data = $response->toArray();
            $books = $this->parseResponse($data);

            foreach ($books as $bookData) {
                $this->saveBookIfNotExists($bookData);
            }
            return $books;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'appel à l\'API Google Books : ' . $e->getMessage());
        }
    }

    private function saveBookIfNotExists(array $bookData): void
    {
        // Check if book already exists in the database
        if ($this->bookRepository->findOneBy(['title' => $bookData['title']])) {
            return;
        }

        $book = new Book();

        $book->setTitle($bookData['title'] ?? 'Titre inconnu')
            ->setAuthors(isset($bookData['authors']) ? implode(', ', $bookData['authors']) : 'Auteur inconnu')
            ->setGenre($bookData['genre'] ?? 'Inconnu')
            ->setIsAvailable(true)
            ->setOverview($bookData['description'] ?? 'Pas de description disponible')
            ->setRating($bookData['rating'] ?? 0)
            ->setImage($bookData['thumbnail'] ?? 'default.jpg');

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
        $this->entityManager->flush();
    }

    
    public function getBookById(string $id): array
    {
        $url = "https://www.googleapis.com/books/v1/volumes/{$id}";
        
        $params = ['key' => $this->apiKey];
    
        try {
            $response = $this->client->request('GET', $url, ['query' => $params]);
            $data = $response->toArray();
            
            return [
                'id' => $data['id'] ?? 'ID non disponible',
                'title' => $data['volumeInfo']['title'] ?? 'Titre inconnu',
                'authors' => $data['volumeInfo']['authors'] ?? ['Auteur inconnu'],
                'description' => $data['volumeInfo']['description'] ?? 'Description non disponible',
                'isbn' => $this->extractIsbn($data['volumeInfo']),
                'thumbnail' => $data['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                'publishedDate' => $data['volumeInfo']['publishedDate'] ?? 'Date non disponible',
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Erreur lors de la récupération du livre : " . $e->getMessage());
        }
    }
        private function parseResponse(array $data): array
    {
        $books = [];
    
        if (!isset($data['items'])) {
            return [];
        }
    
        foreach ($data['items'] as $item) {
            $volumeInfo = $item['volumeInfo'] ?? [];
    
            // Récupération correcte de l'ID du livre
            $bookId = $item['id'] ?? 'ID non disponible';
    
            $books[] = [
                'id' => $bookId,
                'title' => $volumeInfo['title'] ?? 'Titre inconnu',
                'authors' => $volumeInfo['authors'] ?? ['Auteur inconnu'],
                'description' => $volumeInfo['description'] ?? 'Description non disponible',
                'isbn' => $this->extractIsbn($volumeInfo),
                'thumbnail' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
                'publishedDate' => $volumeInfo['publishedDate'] ?? 'Date non disponible',
            ];
        }
    
        return $books;
    }
    
    private function extractIsbn(array $volume): ?string
    {
        foreach ($volume['industryIdentifiers'] ?? [] as $identifier) {
            if ($identifier['type'] === 'ISBN_13') {
                return $identifier['identifier'];
            }
        }
        return null;
    }
}
