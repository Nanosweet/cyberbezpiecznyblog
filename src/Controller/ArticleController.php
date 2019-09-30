<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{slug}", name="app_article")
     */
    public function index($slug, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager -> getRepository(Article::class);
        /** @var Article $article */

        $article = $repository -> findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException(sprintf('no article for slug "%s"', $slug));
        }
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
        ]);
    }
}
