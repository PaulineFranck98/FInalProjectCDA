<?php

namespace App\Controller;

use App\Entity\Itinerary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    #[Route('/favorite/itinerary/{id}', name: 'itinerary_favorite', methods: ['POST'])]
    public function itineraryFavorite( Itinerary $itinerary, EntityManagerInterface $entityManager): JsonResponse 
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        if ($user->getFavoriteItineraries()->contains($itinerary)) {
            $user->removeFavoriteItinerary($itinerary);
            $isFavorite = false;
        } else {
            $user->addFavoriteItinerary($itinerary);
            $isFavorite = true;
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $favoritesCount = $itinerary->getFavoritedBy()->count();

        return new JsonResponse([
            'success' => true,
            'isFavorite' => $isFavorite,
            'favoritesCount' => $favoritesCount,
        ]);
    }
}
