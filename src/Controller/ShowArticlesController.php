<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ShowArticlesController extends AbstractController
{
    /**
     * @Route("/show/articles", name="app_show_articles")
     */
    public function show_articles(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository -> findAllPublishedByNewest();
        return $this->render('show_articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
