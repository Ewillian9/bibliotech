<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleBooksService
{
    private HttpClientInterface $client;
    private ?string $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey = null)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function searchBooks(string $query, int $maxResults = 10): array
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
            return $this->parseResponse($data);
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'appel à l\'API Google Books : ' . $e->getMessage());
        }
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
