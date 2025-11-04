<?php

namespace App\Controller;

use App\Entity\Itinerary;
use App\Form\ItineraryType;
use App\Entity\ItineraryLocation;
use App\HttpClient\ApiHttpClient;
use App\Repository\ItineraryLocationRepository;
use App\Repository\ItineraryRepository;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ItineraryController extends AbstractController
{
    // affichage dans la modale
    #[Route('/api/itineraries', name:'api_user_itineraries')]
    public function userItineraries(): JsonResponse 
    {
         /** @var User $user */
        $user = $this->getUser();

        if(!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $itineraries = $user->getItineraries();

        $data = array_map(function($itinerary) {
            return [
                'id' => $itinerary->getId(),
                'itineraryName' => $itinerary->getItineraryName(),
            ];
        }, $itineraries->toArray());

        return $this->json($data);
    }


    #[Route('/itinerary/new', name:'itinerary_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $itinerary = new Itinerary();

        if (!$this->getUser()) {
            $this->addFlash('error', "Vous devez être connecté pour créer un itinéraire.");
            return $this->redirectToRoute('app_login');
        }

        // l'utilisateur devient directement membre de l'itinéraire
        $itinerary->addUser($this->getUser());
        
        $itinerary->setCreatedBy($this->getUser());

        $form = $this->createForm(ItineraryType::class, $itinerary);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($itinerary);
            $entityManager->flush();

            $this->addFlash('success', 'Itinéraire créé avec succès');

            // je vérifie s'il vient de la page d'un lieu ?
            $locationId = $request->query->get('locationId');
            if($locationId) {
                return $this->redirectToRoute('itinerary_add_location', [
                    'itineraryId' => $itinerary->getId(),
                    'locationId' => $locationId
                ]);
            }

            return $this->redirectToRoute('itinerary_detail', ['itineraryId' => $itinerary->getId()]);
        }

        return $this->render('itinerary/new.html.twig', [ 
            'form' => $form
        ]);
    }


    #[Route('/itinerary/{itineraryId}', name:'itinerary_detail')]
    public function itineraryDetail(int $itineraryId, ApiHttpClient $apiHttpClient, ItineraryRepository $itineraryRepository, ItineraryLocationRepository $itineraryLocationRepository, RatingRepository $ratingRepository) : Response 
    {
        $itinerary = $itineraryRepository->find($itineraryId);

        if(!$itinerary) {
            throw $this->createNotFoundException('Itinéraire introuvable');
        }

        if ($response = $this->ensureUserAccess($itinerary)) {
            return $response;
        }

        $itineraryLocations = $itineraryLocationRepository->findBy(
            ['itinerary' => $itinerary], 
            ['orderIndex' => 'ASC']
        );


        $locations = [];
        foreach($itineraryLocations as $itineraryLocation) {
            $locationId = $itineraryLocation->getLocationId();
            $locationData = $apiHttpClient->getLocation($locationId);
            if($locationData) {
                $locationData['averageRating'] = $ratingRepository->getAverageRating($locationId);

                $locations[] = [
                    'order' => $itineraryLocation->getOrderIndex(),
                    'data' => $locationData,
                ];
            }
        }

        return $this->render('itinerary/show.html.twig', [
            'itinerary' => $itinerary,
            'locations' => $locations,
        ]);
    }

    #[Route('/itinerary/{itineraryId}/edit', name: 'itinerary_edit')]
    public function edit(int $itineraryId, Request $request, EntityManagerInterface $entityManager, ItineraryRepository $itineraryRepository): Response {
        $itinerary = $itineraryRepository->find($itineraryId);

        if (!$itinerary) {
            throw $this->createNotFoundException('Itinéraire introuvable.');
        }

        if ($response = $this->ensureUserAccess($itinerary)) {
            return $response;
        }

        $form = $this->createForm(ItineraryType::class, $itinerary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Itinéraire mis à jour avec succès.');

            return $this->redirectToRoute('itinerary_detail', ['itineraryId' => $itinerary->getId()]);
        }

        return $this->render('itinerary/edit.html.twig', [
            'form' => $form,
            'itinerary' => $itinerary
        ]);
    }

    #[Route('/itinerary/{itineraryId}/delete', name: 'itinerary_delete', methods: ['POST'])]
    public function delete(int $itineraryId, Request $request, EntityManagerInterface $entityManager, ItineraryRepository $itineraryRepository): Response {
        $itinerary = $itineraryRepository->find($itineraryId);

        if (!$itinerary) {
            throw $this->createNotFoundException('Itinéraire introuvable.');
        }

        if ($response = $this->ensureUserAccess($itinerary)) {
            return $response;
        }

        if (!$this->isCsrfTokenValid('delete_itinerary_' . $itinerary->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Échec de la vérification CSRF.');
            return $this->redirectToRoute('itinerary_detail', ['itineraryId' => $itineraryId]);
        }

        $entityManager->remove($itinerary);
        $entityManager->flush();

        $this->addFlash('success', 'Itinéraire supprimé avec succès.');

        return $this->redirectToRoute('app_home');
    }


    #[Route('/itinerary/{itineraryId}/reorder', name: 'itinerary_reorder', methods:['POST'])]
    public function reorderItineraryLocations(int $itineraryId, Request $request, ItineraryRepository $itineraryRepository, ItineraryLocationRepository $itineraryLocationRepository, EntityManagerInterface $entityManager) : JsonResponse 
    {
        $itinerary = $itineraryRepository->find($itineraryId); 

        if(!$itinerary) {
            return $this->json(['error' => 'Itinéraire introuvable'], 404);
        }

        if(!$this->getUser()) {
            return $this->json(['error' => 'Utilisateur non connecté'], 401);
        }

        if(!$itinerary->getUsers()->contains($this->getUser())) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }


        $data = json_decode($request->getContent(), true);

        foreach($data['order'] as $index => $locationId) {
            $itineraryLocation = $itineraryLocationRepository->findOneBy([
                'itinerary' => $itinerary,
                'locationId' => $locationId
            ]);

            if($itineraryLocation) {
                $itineraryLocation->setOrderIndex($index);
            }
        }

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/itinerary/{itineraryId}/add-location/{locationId}', name:'itinerary_add_location')]
    public function addLocationToItinerary(int $itineraryId, string $locationId, EntityManagerInterface $entityManager, ItineraryRepository $itineraryRepository,ItineraryLocationRepository $itineraryLocationRepository) : Response 
    {
        $itinerary = $itineraryRepository->find($itineraryId);

        if(!$itinerary) {
           throw $this->createNotFoundException('Itinéraire introuvable');
        }

        if ($response = $this->ensureUserAccess($itinerary)) {
            return $response;
        }

        // pour empêcher l'ajout du même lieu 2 fois
        $existing = $itineraryLocationRepository->findOneBy([
            'itinerary' => $itinerary,
            'locationId' => $locationId
        ]);

        if($existing) {
            $this->addFlash('warning', "Ce lieu est déjà présent dans l'itinéraire « {$itinerary->getItineraryName()} »");
            return $this->redirectToRoute('itinerary_detail', ['itineraryId' => $itineraryId]);
        }

        $nextOrder = $itineraryLocationRepository->getNextOrderIndex($itinerary);

        $itineraryLocation = new ItineraryLocation();
        $itineraryLocation->setLocationId($locationId);
        $itineraryLocation->setOrderIndex($nextOrder);
        $itineraryLocation->setItinerary($itinerary);

        $entityManager->persist($itineraryLocation);
        $entityManager->flush();

        $this->addFlash('success', "Lieu ajouté à l'itinéraire « {$itinerary->getItineraryName()} »");

        return $this->redirectToRoute('itinerary_detail', [
            'itineraryId' => $itinerary->getId()
        ]);
    }  

    #[Route('/itinerary/{itineraryId}/remove-location/{locationId}', name: 'itinerary_remove_location', methods: ['POST'])]
    public function removeLocationFromItinerary(int $itineraryId, string $locationId, Request $request, ItineraryRepository $itineraryRepository, ItineraryLocationRepository $itineraryLocationRepository, EntityManagerInterface $entityManager): Response {
        $itinerary = $itineraryRepository->find($itineraryId);

        if (!$itinerary) {
            throw $this->createNotFoundException('Itinéraire introuvable');
        }

        if ($response = $this->ensureUserAccess($itinerary)) {
            return $response;
        }

        if(!$this->isCsrfTokenValid('remove_location_' . $locationId, $request->request->get('_token'))) {
            $this->addFlash('error', 'Échec de la vérification CSRF');
            return $this->redirectToRoute('itinerary_detail', ['itineraryId' => $itineraryId]);
        }

        $itineraryLocation = $itineraryLocationRepository->findOneBy([
            'itinerary' => $itinerary,
            'locationId' => $locationId,
        ]);

        $itinerary->removeItineraryLocation($itineraryLocation);

        $entityManager->remove($itineraryLocation);
        $entityManager->flush();

        $this->addFlash('success', 'Lieu retiré de l’itinéraire.');

        return $this->redirectToRoute('itinerary_detail', ['itineraryId' => $itineraryId]);
    }

    private function ensureUserAccess(?Itinerary $itinerary): ?Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', "Vous devez être connecté pour accéder à cette page.");
            return $this->redirectToRoute('app_login');
        }

        if (!$itinerary) {
            throw $this->createNotFoundException('Itinéraire introuvable.');
        }

        if (!$itinerary->getUsers()->contains($this->getUser())) {
            $this->addFlash('error', "Vous n'avez pas accès à cet itinéraire.");
            return $this->redirectToRoute('app_home');
        }

        return null;
    }
}