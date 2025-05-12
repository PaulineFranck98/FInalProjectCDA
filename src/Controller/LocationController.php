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

class LocationController extends AbstractController
{
    #[Route('/location', name: 'locations_list')]
    public function getLocations(ApiHttpClient $apiHttpClient): Response
    {
        $locations = $apiHttpClient->getLocations();
        return $this->render('location/index.html.twig', [
            'locations' => $locations
        ]);
    }

    // new location : form page
    // #[Route('/location/new', name: 'location_form')]
    // public function createLocationForm(): Response
    // {
    //     return $this->render('location/new.html.twig');
    // }

    // #[Route('/location/submit', name: 'location_submit', methods:['POST'])]
    // public function submitLocation(Request $request, HttpClientInterface $httpClient): Response
    // {
    //     $data = [
    //         'locationName' => $request->request->get('locationName'),
    //         'description' => $request->request->get('description'),
    //         'address' => $request->request->get('address'),
    //         'latitude' => $request->request->get('latitude'),
    //         'longitude' => $request->request->get('longitude'),
    //         'mustSee' => $request->request->get('mustSee'),
    //     ];

    //     $response = $httpClient->request('POST', 'http://localhost:3000/api/location', [
    //         'json' => $data
    //     ]);

    //     if($reponse->getStatusCode() === 201) {
    //         $this->addFlash('success', 'Location added successfully!');
    //         return $this->redirectToRoute('locations_list');
    //     }

    //     return new Response('Error while creating location', 500);
    // }
}