<?php

namespace App\Controller;

use App\HttpClient\ApiHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rating;
use App\Form\RatingType;
use App\Repository\RatingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RatingController extends AbstractController
{
    #[Route('/rating', name: 'app_rating')]
    public function index(): Response
    {
        return $this->render('rating/index.html.twig', [
            'controller_name' => 'RatingController',
        ]);
    }

    #[Route('/user/rating/new/{locationId}', name:'new_rating')]
    public function new(Request $request, EntityManagerInterface $entityManager, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository, string $locationId): Response 
    { 
        if (!$this->getUser()) {
            $this->addFlash('error', "Vous devez être connecté pour laisser un avis.");
            return $this->redirectToRoute('app_login');
        }
        
        $rating = new Rating();

        $location = $apiHttpClient->getLocation($locationId);

        $existingRating = $ratingRepository->findOneBy([
            'user' => $this->getUser(),
            'locationId' => $locationId
        ],
        ['ratingDate' => 'DESC']
    );

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()) {
            $rating->setRatingDate(new \DateTimeImmutable());
            $rating->setUser($this->getUser());
            $rating->setLocationId($locationId);

            $entityManager->persist($rating);
            $entityManager->flush();

            $this->addFlash('success', 'Avis ajouté avec succès');

            return $this->redirectToRoute('location_detail', ['id' => $locationId]);

        } elseif ($form->isSubmitted() && !$form->isValid()) {

            $this->addFlash('error', 'Une erreur est survenue, veuillez vérifier le formulaire.');
        }

        return $this->render('rating/new.html.twig', [
            'formAddRating' => $form,
            'location' => $location,
            'existingRating' => $existingRating,
        ]);
    }

    #[Route('/user/rating/{id}/edit', name: 'edit_rating')]
    public function edit(Rating $rating, Request $request, EntityManagerInterface $entityManager, ApiHttpClient $apiHttpClient): Response 
    {
        $location = $apiHttpClient->getLocation($rating->getLocationId());

        if (!$this->getUser()) {
            $this->addFlash('error', "Vous devez être connecté pour modifier un avis.");
            return $this->redirectToRoute('app_login');
        }

     
        if ($rating->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas le droit de modifier cet avis.");
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setRatingDate(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Avis mis à jour avec succès.');
            return $this->redirectToRoute('location_detail', ['id' => $rating->getLocationId()]);
        }

        return $this->render('rating/edit.html.twig', [
            'form' => $form,
            'rating' => $rating,
            'location' => $location
        ]);
    }

    #[Route('/user/rating/{id}/delete', name: 'delete_rating', methods: ['POST'])]
    public function delete(Rating $rating, Request $request, EntityManagerInterface $entityManager): Response 
    {
     
        if (!$this->getUser()) {
            $this->addFlash('error', "Vous devez être connecté pour supprimer un avis.");
            return $this->redirectToRoute('app_login');
        }


        if ($rating->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas le droit de supprimer cet avis.");
            return $this->redirectToRoute('app_home');
        }


        if (!$this->isCsrfTokenValid('delete_rating_' . $rating->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Échec de la vérification CSRF.');
            return $this->redirectToRoute('location_detail', ['id' => $rating->getLocationId()]);
        }

        $entityManager->remove($rating);
        $entityManager->flush();

        $this->addFlash('success', 'Avis supprimé avec succès.');
        return $this->redirectToRoute('location_detail', ['id' => $rating->getLocationId()]);
    }
}
