<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoanController extends AbstractController
{
    #[Route('/loan/return/{id}', name: 'loan_return', methods: ['GET'])]
    public function returnLoan(int $id, LoanRepository $loanRepo, EntityManagerInterface $em): Response
    {
        // Récupérer le prêt
        $loan = $loanRepo->find($id);
        if (!$loan) {
            throw $this->createNotFoundException("Emprunt introuvable");
        }

        // Mettre à jour le statut, par ex. en "Terminé"
        $loan->setStatus("Terminé");
        $em->flush();

        // Message flash
        $this->addFlash('success', 'Livre restitué avec succès !');

        // Rediriger vers la page de profil (ou autre)
        return $this->redirectToRoute('user_profile_view', [
            'id' => $loan->getClient()->getId()
        ]);
    }

    #[Route('/loan/request/{id}', name: 'loan_request')]
    public function requestLoan(
        string $id,                      // l'ID Google ou ID local, à adapter
        BookRepository $bookRepo,
        EntityManagerInterface $em,
        LoanRepository $loanRepo
    ): Response {

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour emprunter un livre.');
            return $this->redirectToRoute('app_login');
        }

        // On compte les prêts "En cours" ou "En attente" de cet utilisateur
        $loansEnCoursOuEnAttente = $loanRepo->createQueryBuilder('l')
            ->where('l.client = :client')
            ->andWhere('l.status IN (:statuts)') // ex: "En cours" ou "En attente"
            ->setParameter('client', $user)
            ->setParameter('statuts', ['En cours', 'En attente'])
            ->getQuery()
            ->getResult();

        if (count($loansEnCoursOuEnAttente) >= 5) {
            // Dépasse la limite de 5 livres
            $this->addFlash('error', 'Vous avez dépassé la limite d’emprunt (5 livres).');
            return $this->redirectToRoute('book_detail', ['id' => $id]);
        }
        // 1) Trouver ou créer le Book en base
        //    Si votre "book.id" = ID Google, vous devez soit importer ce livre dans la DB, 
        //    soit le créer "à la volée". Exemple minimal :

        // Tente de trouver un Book existant avec un champ googleId (si vous l'avez).
        // Sinon, on crée le Book en DB.
        $book = $bookRepo->findOneBy(['googleId' => $id]);
        if (!$book) {
            // Soit on lève une exception
            // throw $this->createNotFoundException("Livre introuvable en DB");

            // Soit on crée un Book minimal en DB avec "En attente" de validation
            $book = new Book();
            $book->setTitle("Livre depuis Google #$id");
            // par défaut, on met 'Inconnu'
            $book->setAuthors('Inconnu');
            $book->setGenre('Inconnu');
            $book->setOverview('Inconnu');
            $book->setRating(0);
            $book->setImage('default.jpg');
            $book->setCategory(null);

            $em->persist($book);
            $em->flush();
        }

        // 2) Créer un nouvel objet Loan
        $loan = new Loan();
        $loan->setBook($book);
        $loan->setClient($this->getUser());
        $loan->setStatus("En attente"); // L'admin devra valider

        // 3) Persister en DB
        $em->persist($loan);
        $em->flush();

        // 4) Message flash de confirmation
        $this->addFlash('success', 'Votre demande d\'emprunt est en attente de confirmation de l\'administrateur.');

        // 5) Redirige vers la page d'accueil, ou le profil
        return $this->redirectToRoute('book_detail', ['id' => $id]);
    }
}
