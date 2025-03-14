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
        
        $params = [
            'key' => $this->apiKey,
        ];
    
        $response = $this->client->request('GET', $url, ['query' => $params]);
    
        // Récupère la réponse de l'API
        $data = $response->toArray();
        
        // Vérifie que 'volumeInfo' existe dans la réponse
        if (!isset($data['volumeInfo'])) {
            throw new \Exception('Détails du livre non disponibles');
        }
    
        // Extraire les informations du livre à partir de 'volumeInfo'
        return $this->parseResponse($data['volumeInfo']);
    }
    private function parseResponse(array $volumeInfo): array
    {
        // Récupère les informations du livre à partir de 'volumeInfo'
        return [
            'id' => $volumeInfo['id'] ?? 'ID non disponible',
            'title' => $volumeInfo['title'] ?? 'Titre inconnu',
            'authors' => $volumeInfo['authors'] ?? ['Auteur inconnu'],
            'description' => $volumeInfo['description'] ?? 'Description non disponible',
            'isbn' => $this->extractIsbn($volumeInfo),
            'thumbnail' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'publishedDate' => $volumeInfo['publishedDate'] ?? 'Date non disponible',
        ];
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
