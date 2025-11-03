<?php

namespace App\Service;

class LocationDistanceService
{
    public function calculateDistance(float $latFrom, float $lonFrom, float $latTo, float $lonTo): float
    {
        $earthRadius = 6371000; 
        $latFrom = deg2rad($latFrom);
        $lonFrom = deg2rad($lonFrom);
        $latTo = deg2rad($latTo);
        $lonTo = deg2rad($lonTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function findNearest(array $locations, float $lat, float $lon, int $limit = 5): array
    {
        $withDistances = [];

        foreach ($locations as $location) {
            if (!isset($location['latitude'], $location['longitude'])) continue;

            $distance = $this->calculateDistance($lat, $lon, (float) $location['latitude'], (float) $location['longitude']);

            $location['distance'] = $distance;
            $withDistances[] = $location;
        }

        usort($withDistances, fn($a, $b) => $a['distance'] <=> $b['distance']);
        return array_slice($withDistances, 0, $limit);
    }
}
