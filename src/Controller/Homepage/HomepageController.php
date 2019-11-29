<?php
/*
 * Kontroler odpowiedzialny za wyswietlenie strony domowej */
namespace App\Controller\Homepage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('homepage/homepage.html.twig');
    }
}
