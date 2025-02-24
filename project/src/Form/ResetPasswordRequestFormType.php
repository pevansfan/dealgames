<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    /**
     * Builds the form for requesting a password reset
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Email field for password reset request
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'], // Helps browsers to autofill the email field
                'constraints' => [
                    // Ensures that the email field is not left empty
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                ],
            ]);
    }

    /**
     * Configures the form's options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        // This form does not map directly to any entity, so no data_class is specified
        $resolver->setDefaults([]);
    }
}