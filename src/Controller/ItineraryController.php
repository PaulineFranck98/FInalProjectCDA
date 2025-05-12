<?php

namespace App\Controller;

use App\Entity\Membre;
use App\HttpClient\ApiHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ItineraryController extends AbstractController
{
    #[Route('/itinerary', name: 'itineraries_list')]
    public function getItineraries(ApiHttpClient $apiHttpClient): Response
    {
        $itineraries = $apiHttpClient->getItineraries();
        return $this->render('itinerary/index.html.twig', [
            'itineraries' => $itineraries
        ]);
    }
    
    // #[Route('/itinerary/new', name: 'itinerary_form')]
    // public function createItineraryForm(ApiHttpClient $apiHttpClient): Response
    // {
    //     $locations = $apiHttpClient->getLocations();
    //     return $this->render('itinerary/new.html.twig', [
    //         'locations' => $locations
    //     ]);
    // }

    // #[Route('/itinerary/submit', name: 'itinerary_submit', methods: ['POST'])]
    // public function submitItinerary(Request $request, HttpClientInterface $httpClient): Response
    // {
    //     $title = $request->request->get('title');
    //     $description = $request->request->get('description');
    //     $locationsRaw = $request->request->all('locations');

    //     $locations = [];

    //     foreach ($locationsRaw as $entry) {
    //         if (empty($entry['id']) == false && empty($entry['order']) == false) {
    //             $locations[] = [
    //                 'id' => $entry['id'],
    //                 'order' => (int) $entry['order']
    //             ];
    //         }
    //     }

    //     $response = $httpClient->request('POST', 'http://localhost:3000/api/itinerary', [
    //         'json' => [
    //             'title' => $title,
    //             'description' => $description,
    //             'locations' => $locations
    //         ]
    //     ]);

    //     if ($response->getStatusCode() === 201) {
    //         $this->addFlash('success', 'Itinéraire créé avec succès !');
    //         return $this->redirectToRoute('itineraries_list');
    //     }

    //     return new Response('Erreur lors de la création de l\'itinéraire.', 500);
    // }
}