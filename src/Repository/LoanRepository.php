<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Loan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loan[]    findAll()
 * @method Loan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    /**
     * Récupère les emprunts en attente pour un client donné
     *
     * @param $clientId L'identifiant du client
     * @return Loan[] Tableau d'emprunts en attente
     */
    public function findPendingLoansByClient($clientId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->andWhere('l.status = :status')
            ->setParameter('client', $clientId)
            ->setParameter('status', 'pending')
            ->orderBy('l.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les emprunts pour un client donné
     *
     * @param $client Le client
     * @return Loan[] Tableau de tous les emprunts
     */
    public function findByClient($client)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.client = :client')
            ->setParameter('client', $client)
            ->orderBy('l.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les emprunts ayant un certain statut
     *
     * @param string $status Le statut de l'emprunt ('pending', 'approved', 'refused', 'returned')
     * @return Loan[] Tableau des emprunts avec ce statut
     */
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.status = :status')
            ->setParameter('status', $status)
            ->orderBy('l.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie le nombre d'emprunts en attente pour un client donné
     *
     * @param $clientId L'identifiant du client
     * @return int Le nombre d'emprunts en attente
     */
    public function countPendingLoans($clientId): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->andWhere('l.client = :client')
            ->andWhere('l.status = :status')
            ->setParameter('client', $clientId)
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère les emprunts en retard
     *
     * @return Loan[] Tableau des emprunts en retard
     */
    public function findOverdueLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.returned_at < :now')
            ->andWhere('l.status != :status')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', 'returned')
            ->orderBy('l.returned_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les emprunts validés (status "approved")
     *
     * @return Loan[] Tableau des emprunts validés
     */
    public function findApprovedLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.status = :status')
            ->setParameter('status', 'approved')
            ->orderBy('l.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
