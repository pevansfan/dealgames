<?php

namespace App\Entity;

use App\Repository\AdRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AdRepository::class)]
#[Vich\Uploadable]
class Ad
{
    // Primary key for the Ad entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Title of the ad, can be null
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    // Description of the ad, can be null
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // Image associated with the ad, mapped via VichUploader
    #[Vich\UploadableField(mapping: 'ads', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    // Name of the uploaded image, stored in the database
    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    // Size of the uploaded image
    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    // Last update date of the ad
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    // Creation date of the ad
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    // Relationship with the Category entity
    #[ORM\ManyToOne(inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    // Relationship with the User entity, the user who created the ad
    #[ORM\ManyToOne(inversedBy: 'user_ads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Getter for the ad ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the ad title
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    // Getter and setter for the ad description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // Setter for the image file. This triggers an update of the updatedAt timestamp
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        // If the image file is provided, update the timestamp
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    // Getter for the image file
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    // Setter for the image name
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    // Getter for the image name
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    // Setter for the image size
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    // Getter for the image size
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    // Getter for the creation timestamp of the ad
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    // Setter for the creation timestamp of the ad
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Returns a human-readable time difference from the ad creation date to the current date.
     * 
     * @return string
     */
    public function getPublishedAtRelative(): string
    {
        $now = new \DateTime();
        $interval = $this->created_at->diff($now);

        // Less than a day old
        if ($interval->d == 0) {
            if ($interval->h == 0) {
                if ($interval->i < 1) {
                    return "Publiée il y a quelques secondes";
                }
                elseif ($interval->i == 1) {
                    return "Publiée il y a $interval->i minute";
                } 
                return "Publiée il y a $interval->i minutes";
            } 
            elseif ($interval->h == 1) {
                return "Publiée il y a $interval->h heure";
            }

            return "Publiée il y a " . $interval->h . " heures";
        } 
        // Published within the last week
        elseif ($interval->d < 7) {
            if ($interval->d == 1) {
                return "Publiée il y a " . $interval->d . " jour";
            }
            return "Publiée il y a " . $interval->d . " jours";
        } 
        // Published more than a week ago
        else {
            return "Publiée le " . $this->created_at->format('d/m/Y');
        }
    }

    // Getter and setter for the category of the ad
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    // Getter and setter for the user who created the ad
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}