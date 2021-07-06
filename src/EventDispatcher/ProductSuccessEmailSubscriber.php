<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        return  [
            'product.view' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info('Le mail à été envoyé pour la visibilité du produit n°' . $productViewEvent->getProduct()->getId());
    }
}
