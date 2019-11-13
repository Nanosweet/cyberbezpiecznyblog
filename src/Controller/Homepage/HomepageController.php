<?php

namespace App\Controller\Homepage;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(/*ArticleRepository $articleRepository*/)
    {
        /* Wybranie artykulow dodanych aktualnego dnia */
       // $articles = $articleRepository->findAllPublishedToday();

        
        return $this->render('homepage/index.html.twig'/*, [
            'articles' =>$articles,
        ]*/);
    }
}
