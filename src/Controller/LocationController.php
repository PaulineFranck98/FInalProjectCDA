<?php

namespace App\Controller;

use App\Entity\Membre;
use App\HttpClient\ApiHttpClient;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\LocationSearchServiceInterface;

class LocationController extends AbstractController
{
    // #[Route('/location', name: 'locations_list')]
    // public function getLocations(ApiHttpClient $apiHttpClient): Response
    // {
    //     $locations = $apiHttpClient->getLocations();
    //     return $this->render('location/index.html.twig', [
    //         'locations' => $locations
    //     ]);
    // }


    #[Route('/location', name: 'location_search')]
    public function search(Request $request, LocationSearchServiceInterface $locationSearchService, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository): Response
    {
        $filters = $request->query->all(); 
        $locations = $locationSearchService->search($filters);

        $types = $apiHttpClient->getFilterData('type');
        $durations = $apiHttpClient->getFilterData('duration');
        $conforts = $apiHttpClient->getFilterData('confort');
        $intensities = $apiHttpClient->getFilterData('intensity');
        $themes = $apiHttpClient->getFilterData('theme');
        $companions = $apiHttpClient->getFilterData('companion');
        $priceRange = $apiHttpClient->getFilterData('location/price-range');

        // & crée une référence : pointeur direct vers l'élément du tableau
        foreach($locations as &$location) {
            $locationId = $location['id'] ?? null;
            $location['averageRating'] = $ratingRepository->getAverageRating($locationId);
        }
        // évite de modifier le dernier élément de $locations
        unset($location);

        return $this->render('location/search.html.twig', [
            'locations' => $locations,
            'filters' => $filters,
            'types' => $types,
            'durations' => $durations,
            'conforts' => $conforts,
            'intensities' => $intensities,
            'themes' => $themes,
            'companions' => $companions,
            'priceRange' => $priceRange,
        ]);
    }

    
    #[Route('/location/{id}', name: 'location_detail')]
    public function getLocation(string $id, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository): Response
    {   
        $location = $apiHttpClient->getLocation($id);

        $averageRating = $ratingRepository->getAverageRating($id);

        $ratings = $ratingRepository->findBy(
            ['locationId' => $id],
            ['ratingDate' => 'DESC']
        );

        return $this->render('location/show.html.twig', [
            'location' => $location,
            'ratings' => $ratings,
            'averageRating' => $averageRating,
        ]);
    }



   
}