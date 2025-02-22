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
    #[Route('/my-announces/update/{id}', name: 'app_update_ad', methods: ['GET', 'POST'])]
    public function update(Ad $ad, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($ad->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier cette annonce.");
        }

        $form = $this->createForm(AnnonceType::class, $ad, [
            'submit_label' => 'Modifier l\'annonce'
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Annonce modifiée avec succès.');

            return $this->redirectToRoute('app_user_ads');
        }

        return $this->render('ad/update.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
        ]);
    }
}