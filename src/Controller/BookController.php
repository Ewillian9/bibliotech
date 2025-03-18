<?php
namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GoogleBooksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Loan;
use App\Repository\LoanRepository;

class BookController extends AbstractController
{
    private GoogleBooksService $googleBooksService;
    private $entityManager;

    public function __construct(GoogleBooksService $googleBooksService, EntityManagerInterface $entityManager)
    {
        $this->googleBooksService = $googleBooksService;
        $this->entityManager = $entityManager;
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
            $user = $this->getUser();
            $bookDetails = $this->googleBooksService->getBookById($id);
            $book = $this->entityManager->getRepository(Book::class)->findOneBy(['googleId' => $id]);
            $loan = $this->entityManager->getRepository(Loan::class)->findOneBy([
                'book' => $book,
                'client' => $user,  // Check if the logged-in user is the client of the loan
                'status' => 'En cours',  // Ensure the loan is active, if needed
            ]);

            return $this->render('book/detail.html.twig', [
                'book' => $bookDetails,
                'hasLoaned' => $loan ? true : false,
            ]);
        } catch (\Exception $e) {
            // Affiche un message d'erreur sans template spécifique
            return new Response('<h1>Erreur</h1><p>Le livre n\'a pas été trouvé ou il y a eu un problème lors de la récupération des détails.</p>', 500);
        }
    }

    #[Route('/book/{id}/pdf', name: 'book_pdf')]
    public function showPdf(string $id): Response
    {
        $user = $this->getUser();
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['googleId' => $id]);
        $loan = $this->entityManager->getRepository(Loan::class)->findOneBy([
            'book' => $book,
            'client' => $user,  // Check if the logged-in user is the client of the loan
            'status' => 'En cours',  // Ensure the loan is active, if needed
        ]);
        
        if (!$user) {
            // If the user is not logged in, throw a 403 Forbidden exception
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à ce document.');
        }

        if (!$loan) {
            // If the user has not loaned the book, deny access
            throw $this->createAccessDeniedException('Vous devez avoir emprunté ce livre pour y accéder.');
        }
        
        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/pdf/book.pdf';

        return $this->file($pdfPath);
    }
}
