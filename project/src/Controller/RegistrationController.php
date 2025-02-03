<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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

            // Encode le mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);

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

            // ✅ Rediriger directement vers la page de vérification
            return $this->redirectToRoute('app_verify_code', ['code' => $verificationCode]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Trouver l'utilisateur à partir du token ou d'un paramètre dans l'URL
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

        // Récupérer l'utilisateur connecté
        /** @var User|null $user */
        $user = $security->getUser();
        dump();
        die(); // 🔥 Sécuriser la récupération de l'utilisateur
        if (!$user) {
            $this->addFlash('error', 'No user is currently logged in.');
            return $this->redirectToRoute('app_register');
        }

        // Vérifier si le code correspond à celui enregistré pour l'utilisateur
        if ($user->getVerificationCode() === $code) {
            $user->setIsVerified(true); // Marquer l'utilisateur comme vérifié
            $user->setVerificationCode(null); // Supprimer le code après vérification réussie
            $entityManager->flush();

            $this->addFlash('success', 'Your email has been successfully verified.');

            return $this->redirectToRoute('app_login'); // Redirection vers la page de connexion
        }

        // Si le code est incorrect
        $this->addFlash('error', 'The verification code is incorrect.');

        return $this->render('registration/verify_code.html.twig', [
            'email' => $user->getEmail(),
        ]);
    }
}
