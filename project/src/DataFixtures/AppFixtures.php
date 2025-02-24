<?php

namespace App\DataFixtures;

use App\Factory\AdFactory;
use App\Factory\CategoryFactory;
use App\Factory\RoleFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Loads the fixture data into the database.
     * 
     * This method will be called when running `php bin/console doctrine:fixtures:load`.
     * It creates roles, users, categories, and ads using the respective factory classes.
     *
     * @param ObjectManager $manager The ObjectManager used to persist the entities.
     */
    public function load(ObjectManager $manager): void
    {   
        // You can modify these values

        // Create predefined roles in the system using the RoleFactory
        RoleFactory::createRoles();

        // Create 10 user entities using the UserFactory
        // The createMany method will generate the specified number of users
        UserFactory::createMany(10);

        // Create predefined categories using the CategoryFactory
        CategoryFactory::createCategories();

        // Create 20 advertisement entities using the AdFactory
        // The createMany method will generate the specified number of ads
        AdFactory::createMany(20);
    }
}
