<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    /**
     * Displays the user profile page.
     *
     * @param AdRepository $adRepository The repository to fetch user ads.
     * @return Response The rendered profile page.
     */
    #[Route('/profile', name: 'app_profile')]
    public function index(AdRepository $adRepository): Response
    {
        $user = $this->getUser();

        // Ensure the user is logged in
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Retrieve ads belonging to the logged-in user
        $annonces = $adRepository->findByUser($user);

        return $this->render('user/profile/index.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
        ]);
    }

    /**
     * Handles profile updates.
     *
     * @param Request $request The HTTP request object.
     * @param EntityManagerInterface $entityManager The entity manager to handle database operations.
     * @param AdRepository $adRepository The repository to fetch user ads.
     * @return Response The profile update form or a redirect after updating.
     */
    #[Route('/profile/update', name: 'app_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, AdRepository $adRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Ensure the user is logged in before allowing updates
        if (!$user) {
            throw $this->createAccessDeniedException('User not logged in.');
        }

        // Retrieve ads belonging to the logged-in user
        $annonces = $adRepository->findByUser($user);

        // Check if the request is a POST request
        if ($request->isMethod('POST')) {
            // Update user details with the submitted data
            $user->setFirstname($request->request->get('firstName', ''));
            $user->setLastname($request->request->get('lastName', ''));
            $user->setEmail($request->request->get('email', ''));

            // Persist changes to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Add a success flash message
            $this->addFlash('success', 'Profile updated successfully.');

            // Redirect to the profile page after update
            return $this->redirectToRoute('app_profile');
        }

        // Render the profile update form
        return $this->render('user/profile/update.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
        ]);
    }
}