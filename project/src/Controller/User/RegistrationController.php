<?php

namespace App\Controller\User;

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
            $plainPassword = trim($form->get('plainPassword')->getData());
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            // Générez un token basé sur l'id et l'email (exemple simple)
            $token = base64_encode($user->getId() . '::' . $user->getEmail());

            // Générez l'URL de vérification
            $verificationUrl = $urlGenerator->generate(
                'app_verify_email',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $email = (new TemplatedEmail())
                ->from(new Address('test@mondomaine.com', 'Evans'))
                ->to($user->getEmail())
                ->subject('Veuillez confirmer votre email')
                ->htmlTemplate('user/registration/confirmation_email.html.twig')
                ->context([
                    'verificationUrl' => $verificationUrl,
                    'user' => $user,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Votre compte a été créé avec succès. Vérifiez votre boîte mail pour confirmer votre adresse.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->query->get('token');

        if (!$token) {
            $this->addFlash('error', 'Lien de vérification invalide.');
            return $this->redirectToRoute('app_register');
        }

        // Décoder le token
        $data = explode('::', base64_decode($token));
        if (count($data) !== 2) {
            $this->addFlash('error', 'Lien de vérification invalide.');
            return $this->redirectToRoute('app_register');
        }

        [$userId, $email] = $data;

        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user || $user->getEmail() !== $email) {
            $this->addFlash('error', 'Utilisateur introuvable ou email invalide.');
            return $this->redirectToRoute('app_register');
        }

        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre compte est déjà vérifié.');
            return $this->redirectToRoute('app_login');
        }

        // Validation de l'utilisateur
        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre email a été confirmé avec succès. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }
}
