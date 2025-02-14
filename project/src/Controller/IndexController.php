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

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(AdRepository $annonceRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $annonces = $annonceRepository->findAll();

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'annonces' => $annonces,
        ]);
    }

    #[Route('/create', name: 'app_create_ad')]
    public function createAd(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Ad();
        $form = $this->createForm(AnnonceType::class, new Ad(), [
            'submit_label' => 'Ajouter l\'annonce'
        ]);
        
        $form->handleRequest($request);
        
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $annonce->setCreatedAt(new \DateTimeImmutable());
            $annonce->setUser($user);
            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce ajoutée avec succès !');
            return $this->redirectToRoute('app_index');
        }

        return $this->render('create/index.html.twig', [
            'controller_name' => 'CreateController',
            'form' => $form->createView()
        ]);
    }
}
