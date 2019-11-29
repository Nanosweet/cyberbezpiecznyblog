<?php

namespace App\Controller\Article;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleNewsListController extends AbstractController
{
    /**
     * @Route("/news", name="app_article_news")
     */
    public function articles_news(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request)
    {
        $articles = $articleRepository -> findAllPublishedLastThreeDays();

        $pagination = $paginator->paginate(
            $articles, $request->query->getInt('page', 1), 5);  // http://geekster.pl/symfony/knppaginatorbundle/
        $pagination->setCustomParameters([
            'size' => 'small',
        ]);
        return $this->render('article_news/article_news.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
