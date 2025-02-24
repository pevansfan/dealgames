<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordFormType extends AbstractType
{
    /**
     * Builds the form for changing the password.
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Adding the password field with repetition (new password and confirmation)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',  // Prevent browsers from autofilling the password
                    ],
                ],
                // Options for the first password field
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',  // Ensures password is not blank
                        ]),
                        new Length([
                            'min' => 12,  // Password must be at least 12 characters
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,  // Max length allowed by Symfony for security reasons
                        ]),
                        new PasswordStrength(),  // Ensures password is strong
                        new NotCompromisedPassword(),  // Ensures password has not been compromised
                    ],
                    'label' => '<ion-icon name="lock-closed"></ion-icon>',  // Custom label with an icon
                    'label_html' => true,  // Allow HTML for the label
                    'row_attr' => ['class' => 'password-field'],  // Adds a class to the field for custom styling
                    'attr' => ['placeholder' => 'Enter your new password'],  // Placeholder text for the field
                ],
                // Options for the second password field (confirmation)
                'second_options' => [
                    'label' => '<ion-icon name="lock-closed"></ion-icon>',  // Custom label with an icon
                    'label_html' => true,  // Allow HTML for the label
                    'row_attr' => ['class' => 'password-field'],  // Adds a class to the field for custom styling
                    'attr' => ['placeholder' => 'Confirm your password'],  // Placeholder text for the field
                ],
                'invalid_message' => 'The password fields must match.',  // Error message if passwords don't match
                'mapped' => false,  // Indicates that this field is not mapped to an entity property
            ]);
    }

    /**
     * Configures the options for the form.
     * 
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        // No additional options are configured for this form
        $resolver->setDefaults([]);
    }
}