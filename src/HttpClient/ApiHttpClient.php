<?php

namespace App\HttpClient;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiHttpClient extends AbstractController
{
    private $client;
    private $baseUrl;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->baseUrl = 'http://localhost:3000/api';
    }

    public function getLocations(): array
    {
        $response = $this->client->request('GET', $this->baseUrl.'/location');
        return $response->toArray();
    }

    public function getItineraries(): array 
    {
        $response = $this->client->request('GET', $this->baseUrl.'/itinerary');
        return $response->toArray();
    }

    public function getLocation(string $id): array
    {
        $response = $this->client->request('GET', $this->baseUrl.'/location/'.$id);
        return $response->toArray();
    }

    public function getItinerary(string $id): array 
    {
        $response = $this->client->request('GET', $this->baseUrl.'/itinerary/'.$id);
        return $response->toArray();
    }

    // public function deleteLocation(string $id): bool 
    // {
    //     $response = $this->client->request('DELETE', $this->baseUrl.'/location/'.$id);
    //     return $response->getStatusCode() === 204;
    // }
}