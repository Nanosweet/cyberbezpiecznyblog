<?php

namespace App\Controller\Article;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleNewsListController extends AbstractController
{
    /**
     * @Route("/articles/news", name="app_articles_news")
     */
    public function articles_news(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository -> findAllPublishedLastThreeDays();
        return $this->render('article_news/articles_news.html.twig', [
            'articles' => $articles,
        ]);
    }
}
