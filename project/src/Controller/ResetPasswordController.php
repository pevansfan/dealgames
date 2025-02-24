<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    /**
     * Constructor to inject the services for password reset functionality.
     *
     * @param ResetPasswordHelperInterface $resetPasswordHelper Helper service for managing password reset.
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     */
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process the form to request a password reset.
     * 
     * @param Request $request The request object containing form data.
     * @param MailerInterface $mailer The mailer service for sending the reset email.
     * @return Response The response object with the rendered view or redirection.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        // Create and handle the password reset request form
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        // If the form is submitted and valid, process the password reset request
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail($email, $mailer);
        }

        // Render the password reset request form
        return $this->render('user/reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     * 
     * @return Response The rendered confirmation page.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone accessed this page directly.
        // This avoids revealing whether the email exists in the system.
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        // Render the confirmation page with the reset token
        return $this->render('user/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and processes the reset URL that the user clicked in their email.
     * 
     * @param Request $request The request object for handling form submission.
     * @param UserPasswordHasherInterface $passwordHasher The service for password hashing.
     * @param string|null $token The reset token from the URL.
     * @return Response The rendered form for resetting the password or redirection.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        if ($token) {
            // Store the token in session to avoid leaking it via the URL
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        // If no token is found, throw an exception
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            // Validate the token and fetch the user associated with it
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            // In case of an error, display a flash message and redirect
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; proceed to allow the user to change their password
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        // If the form is submitted and valid, process the password change
        if ($form->isSubmitted() && $form->isValid()) {
            // Remove the token after use (for security reasons)
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash the new password and update the user's password
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();

            // Clean up the session after the password reset
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_index');
        }

        // Render the password reset form
        return $this->render('user/reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    /**
     * Sends the password reset email to the user.
     * 
     * @param string $emailFormData The email address to send the reset request to.
     * @param MailerInterface $mailer The mailer service for sending the reset email.
     * @return RedirectResponse Redirects to the check-email page after sending the reset email.
     */
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        // Find the user by email
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            // Generate a reset token for the user
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If an error occurs during token generation, redirect to the check email page
            return $this->redirectToRoute('app_check_email');
        }

        // Create the reset email
        $email = (new TemplatedEmail())
            ->from(new Address('test@mondomaine.com', 'Acme Mail Bot'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('user/reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        // Send the email
        $mailer->send($email);

        // Store the token object in session for later use
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}