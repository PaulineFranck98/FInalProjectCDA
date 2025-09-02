<?php

namespace App\Service;

use App\HttpClient\ApiHttpClient;

class LocationSearchService implements LocationSearchServiceInterface
{
    private ApiHttpClient $apiHttpClient;

    public function __construct(ApiHttpClient $apiHttpClient)
    {
        $this->apiHttpClient = $apiHttpClient;
    }
    
    public function search(array $filters = []): array 
    {
        return $this->apiHttpClient->searchLocations($filters);
    }
}