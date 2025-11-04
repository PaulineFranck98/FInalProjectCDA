<?php

namespace App\Controller;

use App\HttpClient\ApiHttpClient;
use App\Repository\UserRepository;
use App\Repository\ItineraryRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route(path: '/admin/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(Security $security, UserRepository $userRepository, ItineraryRepository $itineraryRepository, ApiHttpClient $apiHttpClient) : Response 
    {

        if (!$security->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        $now = new \DateTimeImmutable();
        $oneWeekAgo = $now->modify('-7 days');
        $twoWeeksAgo = $now->modify('-14 days');

        $totalUsers = $userRepository->count([]);
        $usersThisWeek = $userRepository->countUsersRegisteredAfter($oneWeekAgo);
        $usersLastWeek = $userRepository->countUsersRegisteredBetween($twoWeeksAgo, $oneWeekAgo);

        $userGrowth = $usersLastWeek > 0 ? round((($usersThisWeek - $usersLastWeek) / $usersLastWeek) * 100, 1) : ($usersThisWeek > 0 ? 100 : 0);

        $totalItineraries = $itineraryRepository->count([]);
        $itinerariesThisWeek = $itineraryRepository->countCreatedAfter($oneWeekAgo);
        $itinerariesLastWeek = $itineraryRepository->countCreatedBetween($twoWeeksAgo, $oneWeekAgo);

        $itineraryGrowth = $itinerariesLastWeek > 0 ? round((($itinerariesThisWeek - $itinerariesLastWeek) / $itinerariesLastWeek) * 100, 1) : ($itinerariesThisWeek > 0 ? 100 : 0);

        $locationStats = $apiHttpClient->getLocationStats();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'usersThisWeek' => $usersThisWeek,
                'userGrowth' => $userGrowth,
                'totalItineraries' => $totalItineraries,
                'itinerariesThisWeek' => $itinerariesThisWeek,
                'itineraryGrowth' => $itineraryGrowth,
                'totalLocations' => $locationStats['total'] ?? 0,
                'locationsThisWeek' => $locationStats['thisWeek'] ?? 0,
                'locationsGrowth' => isset($locationStats['lastWeek']) && $locationStats['lastWeek'] > 0 ? round((($locationStats['thisWeek'] - $locationStats['lastWeek']) / $locationStats['lastWeek']) * 100, 1) : 0,
            ],
            'latestLocations' => $locationStats['latest'] ?? [],
        ]);
    }

    #[Route(path:'/admin/list-users', name: 'admin_list_users')]
    public function adminListUsers(UserRepository $userRepository, Security $security, Request $request) : Response {

        if (!$security->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        $sort = $request->query->get('sort', 'desc'); 
        $order = $sort === 'asc' ? 'ASC' : 'DESC';

        $users = $userRepository->findBy([], ['registrationDate' => $order]);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'sort' => $sort,
        ]);
    }
}