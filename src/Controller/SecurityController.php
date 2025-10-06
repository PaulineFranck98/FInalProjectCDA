<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

    // profile

    #[Route(path: '/account/profile', name: 'show_profile')]
    public function showProfile(): Response
    {
        return $this->render('profile/profile.html.twig');
    }

    #[Route(path: '/account/ratings', name: 'show_ratings')]
    public function showRatings(): Response
    { 
        return $this->render('security/ratings.html.twig');
    }
}
