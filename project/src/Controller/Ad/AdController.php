<?php

namespace App\Controller\Ad;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdController extends AbstractController
{
    /**
     * Handles the search functionality for ads.
     * 
     * @param Request $request The HTTP request containing the search query.
     * @param AdRepository $adRepository The repository for fetching ads.
     * @return Response The rendered search results page.
     */
    #[Route('/search', name: 'ad_search')]
    public function search(Request $request, AdRepository $adRepository): Response
    {
        // Retrieves the search term from the request query parameters
        $query = $request->query->get('q', '');
        
        // Fetches ads matching the search term
        $ads = $adRepository->search($query);
        
        // Counts the number of results found
        $count = count($ads);

        // Renders the search results template with the ads, query, and count
        return $this->render('ad/search.html.twig', [
            'ads' => $ads,
            'query' => $query,
            'count' => $count,
        ]);
    }

    /**
     * Displays the details of a specific ad.
     * 
     * @param int $id The ID of the ad to display.
     * @param AdRepository $adRepository The repository for fetching ads.
     * @return Response The rendered ad detail page.
     * @throws NotFoundHttpException If the ad is not found.
     */
    #[Route('/products/{id}', name: 'ad_detail')]
    public function detail(int $id, AdRepository $adRepository): Response
    {
        // Fetches the ad by its ID
        $ad = $adRepository->find($id);

        // Throws a 404 error if the ad does not exist
        if (!$ad) {
            throw $this->createNotFoundException("The requested ad does not exist.");
        }

        // Retrieves similar ads based on the same category, excluding the current ad
        $similarAds = $adRepository->findByCategory($ad->getCategory()->getId(), $id);

        // Renders the ad detail template with the ad and similar ads
        return $this->render('ad/detail.html.twig', [
            'ad' => $ad,
            'similarAds' => $similarAds,
        ]);
    }
}