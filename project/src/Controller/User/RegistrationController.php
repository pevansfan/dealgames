<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            /** @var string $confirmPassword */
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Vérification des mots de passe
            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError("Les mots de passe ne correspondent pas."));
            } else {
                // Encode le mot de passe
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
                $user->setRoles(['ROLE_USER']);
                $user->setCreatedAt(new \DateTimeImmutable());

                // Génération du code de vérification
                $verificationCode = random_int(100000, 999999);
                $user->setVerificationCode((string) $verificationCode);

                $entityManager->persist($user);
                $entityManager->flush();

                // Envoyer l'email de confirmation
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('test@mondomaine.com', 'Evans'))
                        ->to((string) $user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        ->context([
                            'verificationUrl' => $this->generateUrl('app_verify_code', [
                                'code' => $verificationCode
                            ], UrlGeneratorInterface::ABSOLUTE_URL),
                            'verification_code' => $verificationCode,
                        ])
                );

                return $this->redirectToRoute('app_verify_code', ['code' => $verificationCode]);
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->query->get('email')]);

        if (!$user) {
            $this->addFlash('error', 'Invalid verification request.');
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        // Rediriger vers la vérification par code
        return $this->redirectToRoute('app_verify_code', ['code' => $user->getVerificationCode()]);
    }


    #[Route('/verify/code', name: 'app_verify_code', methods: ['GET', 'POST'])]
    public function verifyCode(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupération du code envoyé par l'utilisateur (via un formulaire ou une requête POST)
        $code = $request->request->get('verification_code') ?? $request->query->get('code');

        /** @var User|null $user */
        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'No user is currently logged in.');
            return $this->redirectToRoute('app_register');
        }

        if ($user->getVerificationCode() === $code) {
            $user->setIsVerified(true);
            $user->setVerificationCode(null);
            $entityManager->flush();

            $this->addFlash('success', 'Your email has been successfully verified.');

            return $this->redirectToRoute('app_login');
        }

        // Si le code est incorrect
        $this->addFlash('error', 'The verification code is incorrect.');

        return $this->render('registration/verify_code.html.twig', [
            'email' => $user->getEmail(),
        ]);
    }
}
