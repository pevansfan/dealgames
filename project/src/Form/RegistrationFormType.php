<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    /**
     * Builds the registration form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Last name field
            ->add('lastname', TextType::class, [
                'label' => 'Nom', // Label displayed next to the field
                'attr' => ['placeholder' => 'Entrez votre nom'], // Placeholder text
            ])
            // First name field
            ->add('firstname', TextType::class, [
                'label' => 'Prénom', // Label displayed next to the field
                'attr' => ['placeholder' => 'Entrez votre prénom'], // Placeholder text
            ])
            // Email field
            ->add('email')
            // Password field (mapped => false, since the password will not be stored directly)
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // This field does not map directly to the entity property
                'attr' => ['autocomplete' => 'new-password'], // Prevent browsers from autofilling the password
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password', // Error message if password is empty
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters', // Minimum password length
                        'max' => 4096, // Maximum password length
                    ]),
                ],
            ])
            // Checkbox to accept terms and conditions
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false, // This field does not map directly to the entity
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes.', // Error message if not checked
                    ]),
                ],
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
            'data_class' => User::class, // The form maps to the User entity
        ]);
    }
}