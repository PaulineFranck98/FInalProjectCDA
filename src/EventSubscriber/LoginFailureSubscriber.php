<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class LoginFailureSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $securityLogger;
    private RequestStack $requestStack;

    public function __construct(LoggerInterface $securityLogger, RequestStack $requestStack)
    {
        $this->securityLogger = $securityLogger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onLoginFailure(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request ? $request->getClientIp() : 'IP inconnue';

        $userIdentifier = $request->request->get('_username', 'Non spécifié');

        $this->securityLogger->warning("Tentative de connexion échouée pour l'utilisateur '{$userIdentifier}' depuis l'IP : '{$ip}'");
    }
}