<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserDeletionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function anonymizePermanently(User $user): void
    {
        foreach ($user->getRatings() as $rating) {
            $rating->setUser(null);
        }

        foreach ($user->getCreatedItineraries() as $itinerary) {
            $itinerary->setCreatedBy(null);
        }

        foreach ($user->getFavoriteItineraries() as $itinerary) {
            $user->removeFavoriteItinerary($itinerary);
        }

        foreach ($user->getUserVisitedLocations() as $visited) {
            $this->entityManager->remove($visited);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
