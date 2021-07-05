<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function index()
    {

        //1. Nous devons nous assuré que la personne est connecté ->security
        /**
         * @var User
         */
        $user = $this->getUser();

        //2. Savoir qui est connecté->seurity

        //3. Passé l'user à twig pour affiché les commandes->Environment de twig / Response
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
