<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\UserVisitedLocation;
use App\HttpClient\ApiHttpClient;
use App\Repository\RatingRepository;
use App\Repository\UserVisitedLocationRepository;
use App\Service\LocationDistanceService;
use App\Service\LocationSearchServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LocationController extends AbstractController
{
    #[Route('/location', name: 'location_search')]
    public function search(Request $request, LocationSearchServiceInterface $locationSearchService, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository, UserVisitedLocationRepository $visitedRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        $filters = $request->query->all(); 

        $minRating = isset($filters['minRating']) ? (float) $filters['minRating'] : null;

        $result = $locationSearchService->search($filters);

        // gestion d'erreur API Next
        if (isset($result['error'])) {
            $error = $result['error'];
            $message = $error['message'] ?? 'Erreur inconnue';
            $details = $error['details'] ?? [];
            $fullMessage = $details ? $message. ' — ' .implode('; ', $details) : $message;

            $this->addFlash('error', $fullMessage);

            return $this->redirectToRoute('location_search');
        }

        $locations = $result['locations'];
        $pagination = $result['pagination'];

        // & crée une référence : pointeur direct vers l'élément du tableau
        foreach ($locations as &$location) {
            $locationId = $location['id'] ?? null;
            if ($locationId) {
                $location['averageRating'] = $ratingRepository->getAverageRating($locationId);
            }
        }
        // évite de modifier le dernier élément de $locations
        unset($location);

    
        if ($user && !empty($filters['excludeVisited'])) {
            $visitedIds = array_map(fn ($visited) => $visited->getLocationId(), $visitedRepository->findBy(['user' => $user]));

            if ($visitedIds) {
                // je filtre les lieux non visités et réindexe le tableau
                $locations = array_values(array_filter($locations, fn ($location) => !in_array($location['id'], $visitedIds, true)));
            }
        }


        $minRating = (float) ($filters['minRating'][0] ?? 0);
        if ($minRating > 0) {
            // je filtre les lieux selon la note minimale
            $locations = array_values(array_filter($locations, fn ($location) => !empty($location['averageRating']) && $location['averageRating'] >= $minRating));

            // je mets à jour la pagination
            $pagination['total'] = count($locations);
            $pagination['totalPages'] = max(1, ceil($pagination['total'] / $pagination['pageSize']));
        }

        if ($request->isXmlHttpRequest()) {
            // je renvoie uniquement la partie HTML de la liste
            return $this->render('location/partials/_list.html.twig', [
                'locations' => $locations,
                'pagination' => $pagination,
                'filters' => $filters,
            ]);
        }

        return $this->render('location/search.html.twig', [
            'locations' => $locations,
            'pagination' => $pagination,
            'filters' => $filters,
            'types' => $apiHttpClient->getFilterData('type'),
            'durations' => $apiHttpClient->getFilterData('duration'),
            'conforts' => $apiHttpClient->getFilterData('confort'),
            'intensities' => $apiHttpClient->getFilterData('intensity'),
            'themes' => $apiHttpClient->getFilterData('theme'),
            'companions' => $apiHttpClient->getFilterData('companion'),
            'priceRange' => $apiHttpClient->getFilterData('location/price-range'),
        ]);
    }

    
    #[Route('/location/{id}', name: 'location_detail')]
    public function getLocation(string $id, ApiHttpClient $apiHttpClient, RatingRepository $ratingRepository, LocationDistanceService $distanceService): Response
    {   
        $location = $apiHttpClient->getLocation($id);

        if (!isset($location['latitude'], $location['longitude'], $location['zipcode'])) {
            throw $this->createNotFoundException('Coordonnées manquantes');
        }

        $zipcodePrefix = substr($location['zipcode'], 0, 2);

        $nearLocations = $apiHttpClient->getLocationsByZipcode($zipcodePrefix);
        $nearLocations = $nearLocations['data'] ?? $nearLocations; 

        $nearby = $distanceService->findNearest($nearLocations, (float) $location['latitude'], (float) $location['longitude'], 5);

        $currentId = $location['id'] ?? $id;
        $nearby = array_filter($nearby, fn ($loc) => $loc['id'] !== $currentId);

        // $nearby = array_filter($nearby, fn ($location) => $location['id'] !== $location['id']);

        $averageRating = $ratingRepository->getAverageRating($id);

        $ratings = $ratingRepository->findBy(
            ['locationId' => $id],
            ['ratingDate' => 'DESC']
        );

        return $this->render('location/show.html.twig', [
            'location' => $location,
            'ratings' => $ratings,
            'averageRating' => $averageRating,
            'nearby' => $nearby,
        ]);
    } 

    #[Route('/user/visited/location/{id}', name: 'add_visited_location', methods: ['POST'])]
    public function addVisitedLocation(string $id, Request $request, EntityManagerInterface $entityManager, UserVisitedLocationRepository $visitedRepository): JsonResponse 
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $dateString = $data['visitedAt'] ?? null;

        if (!$dateString) {
            return new JsonResponse(['error' => 'Date manquante'], 400);
        }

        $visitedAt = new \DateTimeImmutable($dateString);

        // je vérifie si le lieu est déjà enregistré
        $existing = $visitedRepository->findOneBy([
            'user' => $user,
            'locationId' => $id,
        ]);

        if ($existing) {
            return new JsonResponse(['error' => 'Déjà enregistré comme visité'], 400);
        }

        $visited = new UserVisitedLocation();
        $visited->setUser($user)
                ->setLocationId($id)
                ->setVisitedAt($visitedAt);

        $entityManager->persist($visited);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'locationId' => $id,
            'visitedAt' => $visitedAt->format('Y-m-d'),
        ]);
    }

    #[Route('/user/visited/location/{id}', name: 'remove_visited_location', methods: ['DELETE'])]
    public function removeVisitedLocation(string $id, EntityManagerInterface $entityManager, UserVisitedLocationRepository $visitedRepository ): JsonResponse 
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $existing = $visitedRepository->findOneBy([
            'user' => $user,
            'locationId' => $id,
        ]);

        if (!$existing) {
            return new JsonResponse(['error' => 'Lieu non trouvé'], 404);
        }

        $entityManager->remove($existing);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'locationId' => $id]);
    }
}