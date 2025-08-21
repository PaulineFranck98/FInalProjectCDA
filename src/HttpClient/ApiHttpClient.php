<?php

namespace App\HttpClient;

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
        $response = $this->client->request('GET', $this->baseUrl.'/location/public');
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

    public function searchLocations(array $filters = []): array
    {
        $url = $this->baseUrl . '/location/public';

        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }

        $response = $this->client->request('GET', $url);
        return $response->toArray();
    }

    public function getFilterData(string $endpoint): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/' . $endpoint);
        return $response->toArray();
    }


}