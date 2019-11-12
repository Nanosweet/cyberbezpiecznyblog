<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(ArticleRepository $articleRepository)
    {
        /* Wybranie artykulow dodanych aktualnego dnia */
        $articles = $articleRepository->findAllPublishedToday();

        return $this->render('home_page/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
