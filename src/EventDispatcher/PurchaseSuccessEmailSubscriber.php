<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    private $logger;
    protected $security;
    protected $mailer;
    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->security = $security;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {

        //1 RECUP L USER ACTUELLEMENT EN LIGNE
        /**
         * @var User
         */
        $user = $this->security->getUser();

        //2 RECUP LA COMMANDE
        $purchase = $purchaseSuccessEvent->getPurchase();
        //3 ECRIRE LE MAIL 
        $email = new TemplatedEmail();
        $email->to(new Address($user->getEmail(), $user->getFullName()))
            ->from('contact@mail.com')
            ->subject("Bravo votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $user
            ]);
        //4 ENVOYER L EMAIL
        $this->mailer->send($email);


        $this->logger->info("Email envoyé pour la commande n° " . $purchaseSuccessEvent->getPurchase()->getId());
    }
}
