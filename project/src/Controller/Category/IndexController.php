<?php

namespace App\Controller\Category;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_categories')]
    public function productsByCategory(string $id, AdRepository $productRepository): Response
    {
        $annonces = $productRepository->findByCategory($id);

        return $this->render('category/index.html.twig', [
            'annonces' => $annonces,
            'selectedCategoryId' => $id
        ]);
    }
}
