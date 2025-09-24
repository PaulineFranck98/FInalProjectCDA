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

    #[Route('/rating/new/{locationId}', name:'new_rating')]
    public function new(Request $request, EntityManagerInterface $entityManager, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository, string $locationId): Response 
    { 
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
}
