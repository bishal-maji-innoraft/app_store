<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class MainController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
            return $this->render('index.html.twig');
    }
}