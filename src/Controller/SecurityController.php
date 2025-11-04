<?php

namespace App\Controller;

use App\Entity\User;
use App\HttpClient\ApiHttpClient;
use App\Repository\UserRepository;
use App\Form\ChangePasswordFormType;
use App\Repository\RatingRepository;
use App\Repository\ItineraryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ItineraryLocationRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    // change this part
    public const SCOPES = [
        'google' => [],
    ];

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route("/connect/{service}", name:'connect_google', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if(! in_array($service, array_keys(self::SCOPES), true))
        {
            throw $this->createNotFoundException();
        }


        return $clientRegistry
            ->getClient($service)
            ->redirect(self::SCOPES[$service], []);
    }

    #[Route('/connect/check/{service}', name:'connect_check_google', methods: ['GET', 'POST'])]
    public function check()
    {
        return new Response(status: 200);
    }


    #[Route(path: '/account/dashboard', name: 'show_dashboard')]
    public function showDashboard(ItineraryRepository $itineraryRepository, RatingRepository $ratingRepository, ApiHttpClient $apiHttpClient): Response
    {
        /** @var User $user */
        $user = $this->getUser();

       
        $lastItineraries = $itineraryRepository->findLastByUser($user, 2);


        $ratings = $ratingRepository->findBy(
            ['user' => $user],
            ['ratingDate' => 'DESC'],
            2
        );

          $lastRatings = [];

        foreach ($ratings as $rating) {
            $locationName = null;

            try {
                $location = $apiHttpClient->getLocation($rating->getLocationId());
                $locationName = $location['locationName'] ?? null;
            } catch (\Exception $e) {
                $locationName = null;
            }

            $lastRatings[] = [
                'rating' => $rating,
                'locationName' => $locationName,
            ];
        }

        $stats = [
            'itinerariesCount' => count($user->getItineraries()),
            'ratingsCount' => count($user->getRatings()),
        ];

        return $this->render('profile/dashboard.html.twig', [
            'lastItineraries' => $lastItineraries,
            'lastRatings' => $lastRatings,
            'stats' => $stats,
        ]);
    }


    #[Route(path: '/account/ratings', name: 'show_ratings')]
    public function showRatings(ApiHttpClient $apiHttpClient): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $ratings = $user->getRatings();

        $data = [];

        foreach ($ratings as $rating) {
            $locationName = null;

            try {
                $location = $apiHttpClient->getLocation($rating->getLocationId());
                $locationName = $location['locationName'] ?? null;
            } catch (\Exception $e) {
                $locationName = null;
            }

            $data[] = [
                'rating' => $rating,
                'locationName' => $locationName,
            ];
        }

        return $this->render('profile/ratings.html.twig', [
            'ratingsData' => $data,
        ]);
    }


   #[Route(path: '/account/itineraries', name: 'show_itineraries')]
    public function showItineraries(ItineraryLocationRepository $itineraryLocationRepository, ApiHttpClient $apiHttpClient): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $itineraries = $user->getItineraries();

        $data = [];

        foreach ($itineraries as $itinerary) {
            $first = $itineraryLocationRepository->findFirstByItinerary($itinerary);
            $last = $itineraryLocationRepository->findLastByItinerary($itinerary);

            $departure = null;
            $arrival = null;

            if ($first) {
                try {
                    $location = $apiHttpClient->getLocation($first->getLocationId());
                    $departure = $location['locationName'] ?? null;
                } catch (\Exception $e) {
                    $departure = null;
                }
            }

            if ($last) {
                try {
                    $location = $apiHttpClient->getLocation($last->getLocationId());
                    $arrival = $location['locationName'] ?? null;
                } catch (\Exception $e) {
                    $arrival = null;
                }
            }

            $data[] = [
                'itinerary' => $itinerary,
                'departure' => $departure,
                'arrival' => $arrival,
            ];
        }

        return $this->render('profile/itineraries.html.twig', [
            'itinerariesData' => $data,
        ]);
    }


    // profile
    #[Route(path: '/profile', name: 'show_profile')]
    public function showProfile(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();


        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
            return $this->redirectToRoute('show_profile');
        }

        return $this->render('profile/profile.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }


    #[Route('/profile/update-username', name: 'profile_update_username', methods: ['POST'])]
    public function updateUsername(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $username = trim($data['username'] ?? '');

        if (!$username) {
            return new JsonResponse(['error' => 'Nom invalide.'], 400);
        }

        $user->setUsername($username);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/profile/update-email', name: 'profile_update_email', methods: ['POST'])]
    public function updateEmail(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $email = trim($data['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Adresse e-mail invalide.'], 400);
        }

        
        $user->setEmail($email);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/profile/update-picture', name: 'profile_update_picture', methods: ['POST'])]
    public function updatePicture(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $file = $request->files->get('profilePicture');
        if (!$file) {
            return new JsonResponse(['error' => 'Aucun fichier reçu.'], 400);
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
            return new JsonResponse(['error' => 'Format d’image invalide.'], 400);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getParameter('uploads_directory'), $newFilename);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors du téléchargement du fichier.'], 500);
        }

        if ($user->getProfilePicture() && file_exists($this->getParameter('uploads_directory').'/'.$user->getProfilePicture())) {
            unlink($this->getParameter('uploads_directory').'/'.$user->getProfilePicture());
        }

        $user->setProfilePicture($newFilename);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'newPath' => '/uploads/' . $newFilename
        ]);
    }
}
