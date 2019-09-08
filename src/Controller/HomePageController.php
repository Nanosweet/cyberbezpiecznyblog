<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/homepage", name="homepage")
     */
    public function index()
    {
        return $this->render('home_page/home_page.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }
}
