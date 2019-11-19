<?php

namespace App\Controller\Article;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleListController extends AbstractController
{
    /**
     * @Route("/list", name="app_article_list")
     */
    public function article_list(ArticleRepository $articleRepository)
    {
        $articles =  $articleRepository->findAllPublishedByNewest();
        return $this->render('article_list/article_list.html.twig', [
            'articles' => $articles,
        ]);
    }
}
