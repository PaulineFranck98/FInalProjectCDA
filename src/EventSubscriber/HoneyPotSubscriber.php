<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HoneyPotSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $honeyPotLogger;

    private RequestStack $requestStack;

    public function __construct(
        LoggerInterface $honeyPotLogger, 
        RequestStack $requestStack
    )
    {
        $this->honeyPotLogger = $honeyPotLogger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'checkHoneyJar'
        ];
    }

    public function checkHoneyJar(FormEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if(!$request){
            return;
        }
       
        
        $form = $event->getForm();

        $phone = $form->has('phone') ? trim((string)$form->get('phone')->getData()) : null;
        $faxNumber = $form->has('faxNumber') ? trim((string)$form->get('faxNumber')->getData()) : null;


        if(!empty($phone) || !empty($faxNumber))
        {
            $this->honeyPotLogger->info("Potentielle tentative de robot spammer ayant l'adresse IP suivante : '{$request->getClientIp()}' a eu lieu. Le champ phone contenait '{$phone}' et le champ faxNumber contenait '{$faxNumber}'. Le lien est : '{$request->getURI()}'");   
            // throw new AccessDeniedException("Accès refusé");
            throw new HttpException(403, "Go away dirty bot ! ");
        }
        
    }

}