<?php

namespace App\Controller\Ad;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdController extends AbstractController
{
    #[Route('/mes-annonces', name: 'app_user_ads')]
    #[IsGranted('ROLE_USER')] // S'assure que seul un utilisateur connecté accède à cette route
    public function myAds(AdRepository $adRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $annonces = $adRepository->findByUser($user);

        return $this->render('ad/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }
}
