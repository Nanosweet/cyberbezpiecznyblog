<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/homepage", name="app_homepage")
     */
    public function index()
    {
        return $this->render('home_page/home_page.html.twig', [
            'controller_name' => 'HomePageController',
        ]);
    }
}
