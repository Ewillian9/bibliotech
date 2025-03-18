<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('page/index.html.twig', [
            'titrePage' => 'Bienvenue sur Bibliotech !',
            'books' => $books, // Pass books to the template
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(): Response
    {
        return $this->render('page/search.html.twig');
    }
}