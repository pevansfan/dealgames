<?php

// src/Form/AnnonceType.php
namespace App\Form;

use App\Entity\Ad;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AnnonceType extends AbstractType
{
    /**
     * Builds the form for creating or editing an advertisement.
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Title field: a simple text field
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Titre'],
            ])
            // Description field: a textarea for a longer description
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Description']
            ])
            // Category field: a dropdown list populated with categories
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Display the category name in the dropdown
                'placeholder' => 'Choisir une catégorie', // Placeholder text
                'required' => true, // Make category selection required
            ])
            // Image field: allows the user to upload an image (using VichUploaderBundle)
            ->add('imageFile', VichImageType::class, [
                'required' => false, // Image is not required
                'allow_delete' => false, // Do not allow image deletion
                'constraints' => [
                    new Image([
                        'maxSize' => '10M', // Limit the image size to 10MB
                    ])
                ]
            ])
            // Submit button: customizable label
            ->add('submit', SubmitType::class, [
                'label' => $options['submit_label'], // Allow the submit button's label to be customized
            ]);
    }

    /**
     * Configures the form's options.
     * 
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class, // The form will populate an Ad entity
            'submit_label' => 'Ajouter l\'annonce' // Default label for the submit button
        ]);
    }
}