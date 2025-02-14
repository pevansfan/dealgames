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
    #[Route('/delete/{id}', name: 'app_delete', methods: ['GET', 'POST'])]
    public function delete(Ad $ad, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($ad->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer cette annonce.");
        }

        if ($request->isMethod('POST')) {
            $entityManager->remove($ad);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce supprimée avec succès.');
            return $this->redirectToRoute('app_user_ads');
        }

        return $this->render('ad/delete.html.twig', [
            'ad' => $ad,
        ]);
    }
}
