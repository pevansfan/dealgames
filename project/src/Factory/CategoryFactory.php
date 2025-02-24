<?php

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class CategoryFactory extends PersistentProxyObjectFactory
{
    private static $categories = [
        'Consoles',
        'Jeux',
        'Accessoires',
    ];

    /**
     * Constructor for the CategoryFactory. 
     * Currently, no services are injected, but this could be done if needed.
     */
    public function __construct()
    {
    }

    // Defines the class that this factory creates instances for
    public static function class(): string
    {
        return Category::class;
    }

    /**
     * Returns the default values for Category properties.
     * Currently, only a 'name' property with a null value.
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => null, // The name property is initially set to null
        ];
    }

    /**
     * This method allows further initialization after the Category entity is created. 
     * It's currently empty but can be used to manipulate properties post-instantiation.
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Category $category): void {})
        ;
    }

    /**
     * A helper method to create multiple categories using predefined names.
     * This will create three categories ('Consoles', 'Jeux', 'Accessoires') and persist them to the database.
     */
    public static function createCategories(): void
    {
        foreach (self::$categories as $categoryName) {
            self::createOne(['name' => $categoryName]);
        }
    }
}