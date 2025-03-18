<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 80)]
    private ?string $authors = null;

    #[ORM\Column(length: 80)]
    private ?string $genre = null;

    #[ORM\Column]
    private ?bool $is_available = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Loan::class, mappedBy: 'book')]
    private Collection $loans;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $overview = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
        $this->is_available = true;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthors(): ?string
    {
        return $this->authors;
    }

    // public function setAuthors(string $authors): static
    // {
    //     $this->authors = $authors;
    //     return $this;
    // }
    public function setAuthors(array|string $authors): static
    {
        // Si c'est un tableau, on le transforme en string
        if (is_array($authors)) {
            $authors = implode(', ', $authors);
        }

        $this->authors = $authors;
        return $this;
    }


    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;
        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->is_available;
    }

    public function setIsAvailable(bool $is_available): static
    {
        $this->is_available = $is_available;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): static
    {
        $this->overview = $overview;
        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }
}
