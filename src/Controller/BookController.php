<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController{
    #[Route('/book/{id}', name: 'book_show')]
    public function index(Book $book): Response
    {
        return $this->render('book/index.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/{id}/loan', name: 'book_loan')]
    public function loan(Book $book, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($book->isAvailable()) {
            $book->setIsAvailable(false);
            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été emprunté avec succès !');
        } else {
            $this->addFlash('error', 'Ce livre n\'est pas disponible.');
        }

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }
}
