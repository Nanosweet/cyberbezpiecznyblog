<?php

namespace App\Controller\Article;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleListController extends AbstractController
{
    /**
     * @Route("/articles/list", name="app_articles_list")
     */
    public function article_list(ArticleRepository $articleRepository)
    {
        $articles =  $articleRepository->findAllPublishedByNewest();
        return $this->render('article_list/articles_list.html.twig', [
            'articles' => $articles,
        ]);
    }
}
