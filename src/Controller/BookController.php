<?php
namespace App\Controller;

use App\Service\GoogleBooksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private GoogleBooksService $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

    // Affiche la page d'accueil avec les livres récupérés depuis l'API Google Books
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        // Utilise le service GoogleBooksService pour récupérer les livres 
        $books = $this->googleBooksService->searchBooks( 30); // Limite à 30 livres
        return $this->render('page/index.html.twig', [
            'books' => $books,
        ]);
    }

    
    #[Route('/books/search', name: 'book_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('query', 'Symfony');
        $maxResults = (int) $request->query->get('maxResults', 10);

        try {
            $books = $this->googleBooksService->searchBooks($query, $maxResults);
            return new JsonResponse($books);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la récupération des livres', 'message' => $e->getMessage()], 500);
        }
    }

    // Affiche les détails d'un livre par son ID
    #[Route('/book/{id}', name: 'book_detail', methods: ['GET'])]
    public function bookDetail(string $id): Response
    {
        try {
            // Récupère les détails du livre par son ID
            $bookDetails = $this->googleBooksService->getBookById($id);
            
            return $this->render('book/detail.html.twig', [
                'book' => $bookDetails,
            ]);
        } catch (\Exception $e) {
            // Affiche un message d'erreur sans template spécifique
            return new Response('<h1>Erreur</h1><p>Le livre n\'a pas été trouvé ou il y a eu un problème lors de la récupération des détails.</p>', 500);
        }
    }
}