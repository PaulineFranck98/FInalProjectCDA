<?php

namespace App\Controller;

use App\Entity\Membre;
use App\HttpClient\ApiHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
    public function search(Request $request, ApiHttpClient $apiHttpClient): Response
    {
        $filters = $request->query->all(); 
        $locations = $apiHttpClient->searchLocations($filters);

        $prices = $apiHttpClient->getFilterData('price');
        $types = $apiHttpClient->getFilterData('type');
        $durations = $apiHttpClient->getFilterData('duration');
        $conforts = $apiHttpClient->getFilterData('confort');
        $intensities = $apiHttpClient->getFilterData('intensity');
        $themes = $apiHttpClient->getFilterData('theme');
        $companions = $apiHttpClient->getFilterData('companion');

        return $this->render('location/search.html.twig', [
            'locations' => $locations,
            'filters' => $filters,
            'prices' => $prices,
            'types' => $types,
            'durations' => $durations,
            'conforts' => $conforts,
            'intensities' => $intensities,
            'themes' => $themes,
            'companions' => $companions,
        ]);
    }

    
    #[Route('/location/{id}', name: 'location_detail')]
    public function getLocation(string $id, ApiHttpClient $apiHttpClient): Response
    {   
        $location = $apiHttpClient->getLocation($id);
        return $this->render('location/show.html.twig', [
            'location' => $location
        ]);
    }



   
}