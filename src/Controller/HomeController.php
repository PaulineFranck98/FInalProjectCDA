<?php

namespace App\Controller;

use App\HttpClient\ApiHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ItineraryLocationRepository;
use App\Repository\ItineraryRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ItineraryLocationRepository $itineraryLocationRepository, ItineraryRepository $itineraryRepository, ApiHttpClient $apiHttpClient): Response
    {
        $popularLocations = $itineraryLocationRepository->findMostPopularLocations(3);
        $popularItineraries = $itineraryRepository->findMostFavoritedPublicItineraries(3);

        foreach ($popularLocations as $item) {
            $locationData = $apiHttpClient->getLocation($item['locationId']);

            $locationsData[] = [
                'data' => $locationData,
                'count' => $item['usageCount']
            ];
        };


        return $this->render('home/index.html.twig', [
            'popular_locations' => $locationsData,
            'popular_itineraries' => $popularItineraries
        ]);
    }
}
