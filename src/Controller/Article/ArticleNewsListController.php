<?php
/*
 * Kontroler odpowiedzialny za wyświetlanie listy najnowszych artykułów */

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
        /*
         * Wybranie artykułów udostępnionych w ostatnich 3 dniach
         * Funkcja query w src/Repository/ArticleRepository */
        $articles = $articleRepository->findAllPublishedLastThreeDays();

        /*
         * Dodanie paginacji
         * http://geekster.pl/symfony/knppaginatorbundle/
         * Ustawienie małego rozmiaru paginacji */
        $pagination = $paginator->paginate(
            $articles, $request->query->getInt('page', 1), 5);
        $pagination->setCustomParameters([
            'size' => 'small',
        ]);

        /*
         * Renderowanie widoku */
        return $this->render('article_news/article_news.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
