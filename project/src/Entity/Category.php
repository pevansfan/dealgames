<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    // Primary key for the Category entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The name of the category
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    // Getter for the category ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter for the category name
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter for the category name
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}