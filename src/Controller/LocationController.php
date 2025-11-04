<?php

namespace App\Controller;

use App\Entity\Membre;
use App\HttpClient\ApiHttpClient;
use App\Repository\RatingRepository;
use App\Service\LocationDistanceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LocationSearchServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;

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

        $minRating = isset($filters['minRating']) ? (float)$filters['minRating'] : null;

        $result = $locationSearchService->search($filters);

        // gestion d'erreur API Next
        if(isset($result['error'])) {
            $error = $result['error'];
            $message = $error['message'] ?? 'Erreur inconnue';
            $details = $error['details'] ?? [];
            $fullMessage = $details ? $message . ' — ' . implode('; ', $details) : $message;

            $this->addFlash('error', $fullMessage);

            return $this->redirectToRoute('location_search');
        }

        $locations = $result['locations'];
        $pagination = $result['pagination'];


        // & crée une référence : pointeur direct vers l'élément du tableau
        foreach($locations as &$location) {
            $locationId = $location['id'] ?? null;
            if($locationId) {
                $location['averageRating'] = $ratingRepository->getAverageRating($locationId);
            }
        }
        // évite de modifier le dernier élément de $locations
        unset($location);

        //  filtre par note minimale 
        if ($minRating) {
            $locations = array_filter($locations, function ($loc) use ($minRating) {
                return isset($loc['averageRating']) && $loc['averageRating'] >= $minRating;
            });
            // Réindexe le tableau après filtrage
            $locations = array_values($locations);

             // recalcul pagination après filtrage symfony
            $filteredCount = count($locations);
            $pagination['total'] = $filteredCount;
            $pagination['totalPages'] = max(1, ceil($filteredCount / $pagination['pageSize']));
        }

        if ($request->isXmlHttpRequest()) {
            // je renvoie uniquement la partie HTML de la liste
            return $this->render('location/_list.html.twig', [
                'locations' => $locations,
                'pagination' => $pagination,
                'filters' => $filters,
            ]);
        }

        return $this->render('location/search.html.twig', [
            'locations' => $locations,
            'pagination' => $pagination,
            'filters' => $filters,
            'types' => $apiHttpClient->getFilterData('type'),
            'durations' => $apiHttpClient->getFilterData('duration'),
            'conforts' => $apiHttpClient->getFilterData('confort'),
            'intensities' => $apiHttpClient->getFilterData('intensity'),
            'themes' => $apiHttpClient->getFilterData('theme'),
            'companions' => $apiHttpClient->getFilterData('companion'),
            'priceRange' => $apiHttpClient->getFilterData('location/price-range'),
        ]);
    }

    
    #[Route('/location/{id}', name: 'location_detail')]
    public function getLocation(string $id, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository, LocationDistanceService $distanceService): Response
    {   
        $location = $apiHttpClient->getLocation($id);

        if (!isset($location['latitude'], $location['longitude'], $location['zipcode'])) {
            throw $this->createNotFoundException("Coordonnées manquantes");
        }

        
        $zipcodePrefix = substr($location['zipcode'], 0, 2);

        $nearLocations = $apiHttpClient->getLocationsByZipcode($zipcodePrefix);
        $nearLocations = $nearLocations['data'] ?? $nearLocations; 

        $nearby = $distanceService->findNearest($nearLocations, (float) $location['latitude'], (float) $location['longitude'], 5);

        //  j'exclue le lieu actuel si jamais il est présent
        $nearby = array_filter($nearby, fn($loc) => $loc['id'] !== $location['id']);


        $averageRating = $ratingRepository->getAverageRating($id);

        $ratings = $ratingRepository->findBy(
            ['locationId' => $id],
            ['ratingDate' => 'DESC']
        );

        return $this->render('location/show.html.twig', [
            'location' => $location,
            'ratings' => $ratings,
            'averageRating' => $averageRating,
            'nearby' => $nearby
        ]);
    } 
}