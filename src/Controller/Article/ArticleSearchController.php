<?php
/*
 * Kontroler odpowiedzialny za wyszukiwanie artykułów */

namespace App\Controller\Article;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleSearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_article_search")
     */
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {
        /*
         * Przypisanie zmiennej $q podanego ciągu znaków */
        $q = $request->query->get('q');
        /*
         * Wybranie artykułów zawierających w tytule szukany ciąg znaków
         * Funkcja pytająca src/Repository/ArticleRepository*/
        $articles = $articleRepository->findAllPublishedByTitle($q);

        /*
         * Dodanie paginacji
         * http://geekster.pl/symfony/knppaginatorbundle/
         * Ustawienie małego rozmiaru paginacji */
        $pagination = $paginator->paginate(
            $articles, $request->query->getInt('page', 1), 3);
        $pagination->setCustomParameters([
            'size' => 'small',
        ]);

        /*
         * Renderowanie widoku */
        return $this->render('article_search/article_search.html.twig', [
            'pagination' => $pagination,
            'articles' => $articles,
            'q' => $q
        ]);
    }
}
