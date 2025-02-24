<?php

namespace App\Controller\Ad;

use App\Entity\Ad;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    /**
     * Handles the deletion of an ad.
     *
     * @param Ad $ad The ad entity to be deleted.
     * @param Request $request The HTTP request object.
     * @param EntityManagerInterface $entityManager The entity manager to handle database operations.
     * @return Response The rendered delete confirmation page or a redirect after deletion.
     */
    #[Route('/delete/{id}', name: 'app_delete', methods: ['GET', 'POST'])]
    public function delete(Ad $ad, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the owner of the ad
        if ($ad->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("You are not authorized to delete this ad.");
        }

        // If the request is a POST request, proceed with deletion
        if ($request->isMethod('POST')) {
            $entityManager->remove($ad);
            $entityManager->flush();

            // Add a flash message to notify the user
            $this->addFlash('success', 'Ad successfully deleted.');

            // Redirect to the profile page after deletion
            return $this->redirectToRoute('app_profile');
        }

        // Render the delete confirmation page
        return $this->render('ad/delete.html.twig', [
            'ad' => $ad,
        ]);
    }
}