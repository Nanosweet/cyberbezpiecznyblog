<?php

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

        $articles=  $articleRepository->findAllPublishedByNewest();

        $pagination = $paginator->paginate(
            $articles, $request->query->getInt('page', 1), 3);
        $pagination->setCustomParameters([
            'position' => 'centered',
            'size' => 'small',
            'rounded' => true,
        ]);
        return $this->render('article_list/article_list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
