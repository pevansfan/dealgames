<?php

namespace App\Factory;

use App\Entity\Ad;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Ad>
 */
final class AdFactory extends PersistentProxyObjectFactory
{
    private EntityManagerInterface $em;

    /**
     * Constructor to inject services like EntityManagerInterface
     * 
     * @todo Inject services if required
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // Defines the class that this factory creates instances for
    public static function class(): string
    {
        return Ad::class;
    }

    /**
     * Returns default values for the Ad entity's properties
     * 
     * @todo Add default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'category' => CategoryFactory::random(), // Assign a random Category or null
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month', 'now')),
            'user' => UserFactory::random(),
            'title' => self::faker()->sentence(),
            'description' => self::faker()->paragraph(),
            'imageFile' => self::fakeImageFile(), // Generate a random image
        ];
    }

    /**
     * Helper function to create a fake image file for the Ad entity
     */
    private static function fakeImageFile(): UploadedFile
    {
        $faker = self::faker();
        $imagePath = sys_get_temp_dir() . '/' . $faker->uuid() . '.jpg';

        // Download a random image from picsum.photos
        file_put_contents($imagePath, file_get_contents('https://picsum.photos/640/480'));

        return new UploadedFile(
            $imagePath,
            basename($imagePath),
            'image/jpeg',
            null,
            true
        );
    }

    /**
     * Allows further initialization after object instantiation, e.g., modify properties
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Ad $ad): void {})
        ;
    }
}