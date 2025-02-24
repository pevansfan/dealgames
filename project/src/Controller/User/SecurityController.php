<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * SecurityController handles user authentication.
 */
class SecurityController extends AbstractController
{
    /**
     * Handles the login process.
     *
     * @param AuthenticationUtils $authenticationUtils Provides authentication-related utilities.
     * @return Response The rendered login page.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If the user is already authenticated, redirect to the homepage.
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        // Retrieve the last authentication error, if any.
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Get the last entered username.
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login page with the last username and potential error message.
        return $this->render('user/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Handles user logout.
     *
     * This method is intercepted by Symfony's firewall configuration.
     *
     * @throws \LogicException Always throws an exception, as this method should never be called directly.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method is intentionally left blank and will be handled by Symfony's security system.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
