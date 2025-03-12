<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    private ?Book $Book = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    private ?User $client = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $returned_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->Book;
    }

    public function setBook(?Book $Book): static
    {
        $this->Book = $Book;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeImmutable
    {
        return $this->returned_at;
    }

    public function setReturnedAt(\DateTimeImmutable $returned_at): static
    {
        $this->returned_at = $returned_at;

        return $this;
    }
}
