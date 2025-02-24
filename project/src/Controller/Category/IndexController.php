<?php

namespace App\Controller\Category;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    /**
     * Displays a list of ads filtered by category.
     *
     * @param string $id The category ID.
     * @param AdRepository $productRepository The repository to fetch ads.
     * @return Response The rendered category page with ads.
     */
    #[Route('/category/{id}', name: 'app_categories')]
    public function productsByCategory(string $id, AdRepository $productRepository): Response
    {
        // Fetch all ads belonging to the given category ID
        $annonces = $productRepository->findByCategory($id);

        // Render the category page with the list of ads
        return $this->render('category/index.html.twig', [
            'annonces' => $annonces,
            'selectedCategoryId' => $id
        ]);
    }
}
