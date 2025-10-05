<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiHttpClient
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
    
    public function getLocation(string $id): array
    {
        $response = $this->client->request('GET', $this->baseUrl.'/location/'.$id);
        return $response->toArray();
    }

    public function getItineraries(): array 
    {
        $response = $this->client->request('GET', $this->baseUrl.'/itinerary');
        return $response->toArray();
    }

    // public function searchLocations(array $filters = []): array
    // {
    //     $url = $this->baseUrl . '/location/public';

    //     if (!empty($filters)) {
    //         $url .= '?' . http_build_query($filters);
    //     }

    //     $response = $this->client->request('GET', $url);
    //     return $response->toArray();
    // }

    public function searchLocations(array $filters = []): array
    {
        $url = $this->baseUrl . '/location/public';

        // contiendra le sparamètres de la requête
        $query = [];
        foreach ($filters as $key => $value) {
            // si le filtre contient plusieurs valeurs
            if (is_array($value)) {
                // transforme ['a','b'] en durationId[]=a&durationId[]=b
                foreach ($value as $v) {
                    // urlencode permet d'éviter les problèmes avec les caractères spéciaux
                    $query[] = urlencode($key.'[]') . '=' . urlencode($v);
                }
            } else {
                $query[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        // si on a au moins un filtre, on colle tout à la suite de l'url
        if (!empty($query)) {
            $url .= '?' . implode('&', $query);
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