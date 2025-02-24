<?php

namespace App\Controller\Ad;

use App\Entity\Ad;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UpdateController extends AbstractController
{
    /**
     * Handles the update of an ad.
     * 
     * @param Ad $ad The ad entity to be updated.
     * @param Request $request The HTTP request object.
     * @param EntityManagerInterface $entityManager The entity manager to handle database operations.
     * @return Response The rendered update form page or a redirect after updating.
     */
    #[Route('/my-announces/update/{id}', name: 'app_update_ad', methods: ['GET', 'POST'])]
    public function update(Ad $ad, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Ensure the user is the owner of the ad before allowing modifications
        if ($ad->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("You are not authorized to modify this ad.");
        }

        // Create the update form, passing the existing ad data
        $form = $this->createForm(AnnonceType::class, $ad, [
            'submit_label' => 'Update Ad'
        ]);
        
        // Handle form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Save changes to the database
            $entityManager->flush();

            // Redirect the user to their profile page after update
            return $this->redirectToRoute('app_profile');
        }

        // Render the update form page
        return $this->render('ad/update.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
        ]);
    }
}