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
    // récupération des itinéraires pour affichage dans la modal
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

        // l'utilisateur devient directement membre de l'itinéraire
        $itinerary->addUser($this->getUser());

        $form = $this->createForm(ItineraryType::class, $itinerary);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($itinerary);
            $entityManager->flush();

            $this->addFlash('success', 'Itinéraire créé avec succès');

            // je vérifie s'il vient de la page d'un lieu ?
            $locationId = $request->query->get('locaitonId');
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

        // récupère les lieux associés à l'itinéraire
        $itineraryLocations = $itineraryLocationRepository->findBy(
            ['itinerary' => $itinerary], 
            ['orderIndex' => 'ASC']
        );

        // récupère les détails de chaque lieu
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

    #[Route('/itinerary/{itineraryId}/reorder', name: 'itinerary_reorder', methods:['POST'])]
    public function reorderItineraryLocations(int $itineraryId, Request $request, ItineraryRepository $itineraryRepository, ItineraryLocationRepository $itineraryLocationRepository, EntityManagerInterface $entityManager) : JsonResponse 
    {
        $itinerary = $itineraryRepository->find($itineraryId); 

        if(!$itinerary) {
            return $this->json(['error' => 'Itinéraire introuvable'], 404);
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

        // pour empêcher l'ajout du même lieu 2 fois
        $existing = $itineraryLocationRepository->findOneBy([
            'itinerary' => $itinerary,
            'locationId' => $locationId
        ]);

        if($existing) {
            $this->addFlash('warning', "Celieu est déjà présent dans l'itinéraire « {$itinerary->getItineraryName()} »");
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

        // vérification du token
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
}