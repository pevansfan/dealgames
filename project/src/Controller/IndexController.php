<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for managing ads and rendering the homepage.
 */
final class IndexController extends AbstractController
{
    /**
     * Display the homepage with a list of ads.
     *
     * @param AdRepository $annonceRepository The repository to fetch ads from the database.
     * @return Response The rendered view with the list of ads.
     */
    #[Route('/', name: 'app_index')]
    public function index(AdRepository $annonceRepository): Response
    {
        // Check if the user is logged in, if not redirect to login page
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Retrieve all ads from the database
        $annonces = $annonceRepository->findAll();

        // Render the homepage template and pass the ads data
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'annonces' => $annonces,
        ]);
    }

    /**
     * Create a new ad.
     *
     * @param Request $request The HTTP request object.
     * @param EntityManagerInterface $entityManager The entity manager to save the ad to the database.
     * @return Response The rendered form view or redirect after success.
     */
    #[Route('/create', name: 'app_create_ad')]
    public function createAd(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instantiate a new Ad object
        $annonce = new Ad();
        
        // Create the form for adding an ad, passing the form type and custom submit button label
        $form = $this->createForm(AnnonceType::class, $annonce, [
            'submit_label' => 'Ajouter l\'annonce'
        ]);
        
        // Handle the form submission
        $form->handleRequest($request);
        
        // Get the currently logged-in user
        $user = $this->getUser();
        
        // Check if the form was submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the creation date and associate the ad with the user
            $annonce->setCreatedAt(new \DateTimeImmutable());
            $annonce->setUser($user);
            
            // Persist the new ad to the database
            $entityManager->persist($annonce);
            $entityManager->flush();

            // Add a success flash message and redirect to the homepage
            $this->addFlash('success', 'Annonce ajoutée avec succès !');
            return $this->redirectToRoute('app_index');
        }

        // Render the form view for creating an ad
        return $this->render('ad/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}