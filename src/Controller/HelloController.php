<?php

namespace App\Controller;

use App\Taxes\Detector;
use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    protected $calculator;
    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
    /**
     * @Route("/hello/{prenom<\D+>?world}", name="hello")
     */
    public function hello($prenom)
    {
        return $this->render("hello.html.twig", ["prenom" => $prenom]);
    }
    /**
     * @Route("/example", name="example")
     */
    public function example()
    {
        return $this->render("example.html.twig", ["age" => 33]);
    }
}
