<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HoneyPotSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    private RequestStack $requestStack;

    public function __construct(
        LoggerInterface $logger, 
        RequestStack $requestStack
    )
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
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
        // dd($event);

        // $data = $event->getData();
        $form = $event->getForm();

        $phone = $form->has('phone') ? trim((string)$form->get('phone')->getData()) : null;
        $faxNumber = $form->has('faxNumber') ? trim((string)$form->get('faxNumber')->getData()) : null;


        if(!empty($phone) || !empty($faxNumber))
        {
            $this->logger->info("Potentielle tentative de robot spammer ayant l'adresse IP suivante : '{$request->getClientIp()}' a eu lieu. Le champ phone contenait '{$phone}' et le champ faxNumber contenait '{$faxNumber}'");   
            throw new HttpException(403, "Go away dirty bot !");
        }
        
    }

}