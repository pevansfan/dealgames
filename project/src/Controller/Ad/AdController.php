<?php

namespace App\Controller\Ad;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdController extends AbstractController
{
    #[Route('/my-annonces', name: 'app_user_ads')]
    public function myAds(AdRepository $adRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $annonces = $adRepository->findByUser($user);

        return $this->render('user/profile/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/search', name: 'ad_search')]
    public function search(Request $request, AdRepository $adRepository): Response
    {
        $query = $request->query->get('q', ''); // Récupère le terme de recherche
        $ads = $adRepository->search($query);

        return $this->render('ad/search.html.twig', [
            'ads' => $ads,
            'query' => $query,
        ]);
    }
}
