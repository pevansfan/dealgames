<?php

namespace App\Factory;

use App\Entity\Role;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Role>
 */
final class RoleFactory extends PersistentProxyObjectFactory
{
    private static $roles = [
        "ROLE_USER",
        "ROLE_ADMIN"
    ];

    /**
     * Constructor for the RoleFactory.
     * No services are injected at the moment, but this can be expanded in the future.
     */
    public function __construct()
    {
    }

    // Defines the class that this factory creates instances for
    public static function class(): string
    {
        return Role::class;
    }

    /**
     * Returns the default values for Role properties.
     * A role is chosen randomly from the predefined list (`ROLE_USER`, `ROLE_ADMIN`).
     */
    protected function defaults(): array|callable
    {
        $roleName = self::$roles[array_rand(self::$roles)]; // Randomly choose a role from the predefined list

        return [
            'name' => $roleName, // The randomly chosen role name
        ];
    }

    /**
     * This method allows further initialization after the Role entity is created.
     * It's currently empty but can be expanded in the future.
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Role $role): void {})
        ;
    }

    /**
     * Helper method to create multiple roles (`ROLE_USER` and `ROLE_ADMIN`) and persist them to the database.
     */
    public static function createRoles(): void
    {
        foreach (self::$roles as $roleName) {
            self::createOne(['name' => $roleName]);
        }
    }
}