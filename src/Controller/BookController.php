<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController{
    #[Route('/book/{id}', name: 'book_show')]
    public function index(Book $book): Response
    {
        return $this->render('book/index.html.twig', [
            'book' => $book,
        ]);
    }
}
