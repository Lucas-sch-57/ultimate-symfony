<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
        //     $email = new TemplatedEmail();
        //     $email->from(new Address('contact@mail.com', 'Infos de la boutique'))
        //         ->to('admin@mail.com')
        //         ->text('Un visiteur est entrain de voir la page du produit n°' . $productViewEvent->getProduct()->getId())
        //         ->subject('Visite du produit n°' . $productViewEvent->getProduct()->getId())
        //         ->htmlTemplate('emails/product_view.html.twig')
        //         ->context([
        //             'product' => $productViewEvent->getProduct(),
        //         ]);

        //     $this->mailer->send($email);
        $this->logger->info('Le mail à été envoyé pour la visibilité du produit n°' . $productViewEvent->getProduct()->getId());
    }
}
