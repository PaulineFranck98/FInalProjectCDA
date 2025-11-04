<?php

namespace App\Controller;

use App\Entity\User;
use App\HttpClient\ApiHttpClient;
use App\Repository\RatingRepository;
use App\Repository\ItineraryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ItineraryLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PublicController extends AbstractController
{
    #[Route(path: '/public/user/{id}', name: 'public_profile')]
    public function publicProfile(User $user, ItineraryRepository $itineraryRepository, RatingRepository $ratingRepository, ApiHttpClient $apiHttpClient): Response 
    {

        $itineraries = $itineraryRepository->findBy(
            ['createdBy' => $user, 'isPublic' => true],
            ['departureDate' => 'DESC']
        );

        $itinerariesData = [];
        foreach ($itineraries as $itinerary) {
            $itinerariesData[] = [
                'itinerary' => $itinerary,
                // départ/arrivée ici plus tard
            ];
        }

        // avis
        $ratings = $ratingRepository->findBy(
            ['user' => $user],
            ['ratingDate' => 'DESC']
        );

        $ratingsData = [];
        foreach ($ratings as $rating) {
            $locationName = null;
            try {
                $location = $apiHttpClient->getLocation($rating->getLocationId());
                $locationName = $location['locationName'] ?? null;
            } catch (\Exception $e) {
                $locationName = null;
            }

            $ratingsData[] = [
                'rating' => $rating,
                'locationName' => $locationName,
            ];
        }

        return $this->render('profile/public_profile.html.twig', [
            'userProfile' => $user,
            'itinerariesData' => $itinerariesData,
            'ratingsData' => $ratingsData,
        ]);
    }

    #[Route('/public/itinerary/{itineraryId}', name: 'public_itinerary_detail')]
    public function publicItineraryDetail(int $itineraryId, ApiHttpClient $apiHttpClient, ItineraryRepository $itineraryRepository, ItineraryLocationRepository $itineraryLocationRepository, RatingRepository $ratingRepository): Response 
    {
        $itinerary = $itineraryRepository->find($itineraryId);

        if (!$itinerary) {
            throw $this->createNotFoundException('Itinéraire introuvable');
        }

        if (!$itinerary->isPublic()) {
            throw $this->createAccessDeniedException('Cet itinéraire est privé.');
        }

        $itineraryLocations = $itineraryLocationRepository->findBy(
            ['itinerary' => $itinerary],
            ['orderIndex' => 'ASC']
        );

        $locations = [];
        foreach ($itineraryLocations as $itineraryLocation) {
            $locationId = $itineraryLocation->getLocationId();
            $locationData = $apiHttpClient->getLocation($locationId);

            if ($locationData) {
                $locationData['averageRating'] = $ratingRepository->getAverageRating($locationId);
                $locations[] = [
                    'order' => $itineraryLocation->getOrderIndex(),
                    'data' => $locationData,
                ];
            }
        }

        return $this->render('itinerary/public_show.html.twig', [
            'itinerary' => $itinerary,
            'locations' => $locations,
        ]);
    }

}