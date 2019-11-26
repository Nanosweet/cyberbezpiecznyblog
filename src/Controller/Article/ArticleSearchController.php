<?php

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
        $q = $request->query->get('q');
        $articles = $articleRepository->findAllPublishedByTitle($q);

        $pagination = $paginator->paginate(
            $articles, $request->query->getInt('page', 1), 3);  // http://geekster.pl/symfony/knppaginatorbundle/
        $pagination->setCustomParameters([
            'size' => 'small',
        ]);

        return $this->render('article_search/article_search.html.twig', [
            'pagination' => $pagination,
            'articles' => $articles,
            'q' => $q
        ]);
    }
}
