<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(AdRepository $adRepository): Response
    {
        $user = $this->getUser();

        // Vérifier si un utilisateur est bien connecté
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $annonces = $adRepository->findByUser($user);

        return $this->render('user/profile/index.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
        ]);
    }

    #[Route('/profile/update', name: 'app_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, AdRepository $adRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();


        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $annonces = $adRepository->findByUser($user);

        // Vérifie si la requête est bien en POST
        if ($request->isMethod('POST')) {
            $user->setFirstname($request->request->get('firstName', ''));
            $user->setLastname($request->request->get('lastName', ''));
            $user->setEmail($request->request->get('email', ''));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profile/update.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
        ]);
    }
}
