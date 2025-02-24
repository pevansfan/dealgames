<?php

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    // Including the ResetPasswordRequestTrait to handle common logic related to reset password requests
    use ResetPasswordRequestTrait;

    // Primary key for the ResetPasswordRequest entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The user associated with the password reset request
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Constructor to initialize a password reset request with user, expiration time, selector, and token
    public function __construct(User $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    // Getter for the reset password request ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter for the user associated with the reset password request
    public function getUser(): User
    {
        return $this->user;
    }
}