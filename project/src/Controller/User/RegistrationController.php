<?php

namespace App\Controller\User;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    /**
     * Handles user registration.
     * 
     * @param Request $request The HTTP request object.
     * @param UserPasswordHasherInterface $userPasswordHasher The password hasher service.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param MailerInterface $mailer The mailer service.
     * @param UrlGeneratorInterface $urlGenerator The URL generator service.
     * 
     * @return Response The HTTP response.
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the user's password before storing it
            $plainPassword = trim($form->get('plainPassword')->getData());
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setCreatedAt(new \DateTimeImmutable());
            
            // Assign default user role
            $roleUser = $entityManager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_USER']);
            if ($roleUser) {
                $user->addRole($roleUser);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // Generate a verification token (simple base64 encoding)
            $token = base64_encode($user->getId() . '::' . $user->getEmail());

            // Generate the email verification URL
            $verificationUrl = $urlGenerator->generate(
                'app_verify_email',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            // Create the verification email
            $email = (new TemplatedEmail())
                ->from(new Address('test@mondomaine.com', 'Evans'))
                ->to($user->getEmail())
                ->subject('Please confirm your email')
                ->htmlTemplate('user/registration/confirmation_email.html.twig')
                ->context([
                    'verificationUrl' => $verificationUrl,
                    'user' => $user,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Your account has been successfully created. Check your email to confirm your address.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Verifies a user's email based on a token sent via email.
     * 
     * @param Request $request The HTTP request containing the verification token.
     * @param EntityManagerInterface $entityManager The entity manager.
     * 
     * @return Response The HTTP response.
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->query->get('token');

        if (!$token) {
            $this->addFlash('error', 'Invalid verification link.');
            return $this->redirectToRoute('app_register');
        }

        // Decode the token
        $data = explode('::', base64_decode($token));
        if (count($data) !== 2) {
            $this->addFlash('error', 'Invalid verification link.');
            return $this->redirectToRoute('app_register');
        }

        [$userId, $email] = $data;

        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user || $user->getEmail() !== $email) {
            $this->addFlash('error', 'User not found or email mismatch.');
            return $this->redirectToRoute('app_register');
        }

        if ($user->isVerified()) {
            $this->addFlash('info', 'Your account is already verified.');
            return $this->redirectToRoute('app_login');
        }

        // Mark the user as verified
        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Your email has been successfully confirmed. You can now log in.');

        return $this->redirectToRoute('app_login');
    }
}
