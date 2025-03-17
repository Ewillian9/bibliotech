<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * Page d'accueil : liste tous les livres stockés en base.
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(BookRepository $bookRepository): Response
    {
        // Récupère tous les livres dans la DB
        $books = $bookRepository->findAll();

        // Affiche la vue Twig "page/index.html.twig"
        return $this->render('page/index.html.twig', [
            'books' => $books,  // On passe les entités Book
        ]);
    }

    /**
     * Page de détails : affiche un livre à partir de son ID local en DB.
     */
    #[Route('/books/{id}', name: 'book_detail', methods: ['GET'])]
    public function bookDetail(int $id, BookRepository $bookRepository): Response
    {
        // Récupère le livre depuis la DB grâce à l'ID
        $book = $bookRepository->find($id);

        if (!$book) {
            // Lève une exception si le livre est introuvable
            throw $this->createNotFoundException("Livre introuvable !");
        }

        // Affiche la vue "book/detail.html.twig" avec l'entité Book
        return $this->render('book/detail.html.twig', [
            'book' => $book,
        ]);
    }
}

