<?php

namespace App\Factory;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private $userPasswordHasher;
    private $entityManager;

    /**
     * Constructor for UserFactory. Injecting services for password hashing and entity manager.
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the class name for the entity this factory creates (User).
     */
    public static function class(): string
    {
        return User::class;
    }

    /**
     * Default values for User entity properties.
     * A random email, name, and password (hashed) are generated using Faker.
     */
    protected function defaults(): array|callable
    {
        // Creating a new User instance to hash the password
        $user = new User();
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, 'test'); // Hashing 'test' as password

        return [
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->email(50),
            'firstname' => self::faker()->firstName(),
            'isVerified' => self::faker()->boolean(true),
            'lastname' => self::faker()->lastName(),
            'password' => $hashedPassword,
            // Optionally, you can also assign roles if required here
        ];
    }

    /**
     * Allows additional initialization after the User entity is created.
     * Currently, no extra initialization is done, but can be extended later.
     */
    protected function initialize(): static
    {
        return $this;
        // ->afterInstantiate(function (User $user): void {})
    }
}