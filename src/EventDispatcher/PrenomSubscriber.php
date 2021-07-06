<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'addPrenomToAttributes',
        ];
    }
    public function addPrenomToAttributes(RequestEvent $requestEvent)
    {
        $requestEvent->getRequest()->attributes->set('prenom', 'Lior');
    }
    public function test1()
    {
    }
    public function test2()
    {
    }
}
