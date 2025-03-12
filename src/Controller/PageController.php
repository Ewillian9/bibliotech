<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'titrePage' => 'Bienvenue sur Bibliotech !',
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(): Response
    {
        return $this->render('page/search.html.twig');
    }
}