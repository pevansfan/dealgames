<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Primary key for the User entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The email address, which is unique for each user
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    // Many-to-many relationship with Role entity (users have roles)
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roles;

    // The hashed password for the user
    #[ORM\Column]
    private ?string $password = null;

    // Last name of the user
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    // First name of the user
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    // A flag indicating whether the user is verified (e.g., via email)
    #[ORM\Column]
    private ?bool $isVerified = false;

    // Timestamp of when the user was created
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    // Constructor initializing the roles collection
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    // Getter for the user ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the email address
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles->map(fn(Role $role) => $role->getName())->toArray();

        // Ensuring the user has at least ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    // Adds a role to the user
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    // Removes a role from the user
    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @return Collection<int, Role>  Returns a collection of Role entities associated with the user
     */
    public function getRolesEntities(): Collection
    {
        return $this->roles;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Sets the user's password
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If there are any sensitive, temporary data to clear, it can be done here
    }

    // Getter and setter for the user's last name
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    // Getter and setter for the user's first name
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    // Getter and setter for the user's verification status
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // Getter and setter for the user's creation timestamp
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}