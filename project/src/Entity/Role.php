<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    // Primary key for the Role entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Name of the role, unique for each role
    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    // Many-to-many relationship with User entity (users assigned this role)
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private Collection $users;

    // Constructor initializing the users collection
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    // Getter for the role ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter for the role name
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter for the role name
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>  Returns a collection of User objects associated with this role
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    // Adds a User to the role (many-to-many relation)
    public function addUser(User $user): static
    {
        // Avoid duplicates in the collection
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRole($this); // Add the role to the user's roles
        }

        return $this;
    }

    // Removes a User from the role (many-to-many relation)
    public function removeUser(User $user): static
    {
        // Check if the user is in the collection
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeRole($this); // Remove the role from the user's roles
        }

        return $this;
    }
}