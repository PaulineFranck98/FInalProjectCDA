<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiHttpClient
{
    private $client;
    private $baseUrl;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->baseUrl = $_ENV['NEXT_API_BASE_URL'] ?? 'http://localhost:3000/api/v1';
    }

 
    
    public function getLocation(string $id): ?array
    { 
        $response = $this->client->request('GET', $this->baseUrl . '/location/' . $id);

        $data = $response->toArray(false);

        if ($response->getStatusCode() !== 200 || empty($data)) {
            return null;
        }

        return $data;
    }

    public function getLocationsByZipcode(string $zipcode): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/public/location/by-zipcode/' . $zipcode);
        return $response->toArray();
    }


    public function searchLocations(array $filters = []): array
    {
        $url = $this->baseUrl . '/public/location';

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $filters[$key] = implode(',', $value);
            }
        }

        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }

        $response = $this->client->request('GET', $url);
        $status = $response->getStatusCode();

        // false pour ne pas jeter d'erreur automatiquement si erreur !== 200
        $data = $response->toArray(false);

        if($status !== 200) {
            $errorMessage = $data['error'] ?? 'Une erreur est survenue.';
            $details =$data['details'] ?? [];
            
            return [
                'error' => [
                    'status' => $status,
                    'message' => $errorMessage,
                    'details' => $details,
                ]
            ];
        }

        // check si la réponse contient bien les données attendues
        return [
            'locations' => $data['data'] ?? [],
            'pagination' => [
                'page' => $data['page'] ?? 1,
                'pageSize' => $data['pageSize'] ?? 10,
                'total' => $data['total'] ?? 0,
                'totalPages' => $data['totalPages'] ?? 1,
            ]
        ];
    }

    public function getFilterData(string $endpoint): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/' . $endpoint);
        return $response->toArray();
    }

    public function getLocationStats(): array
    {
        $response = $this->client->request('GET', $this->baseUrl.'/public/location/stats');
        $data = $response->toArray(false);

        return $data;
    }
}