<?php
/*
 * Kontroler odpowiedzialny za wyświetlanie listy artykułów */

namespace App\Controller\Article;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleListController extends AbstractController
{
    /**
     * @Route("/list", name="app_article_list")
     */
    public function article_list(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request)
    {
        /*
         * Wybranie artykułów od najnowszego
         * Funkcja query w src/Repository/ArticleRepository */
        $articles = $articleRepository->findAllPublishedByNewest();

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
        return $this->render('article_list/article_list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
