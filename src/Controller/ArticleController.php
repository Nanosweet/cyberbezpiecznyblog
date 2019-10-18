<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{slug}", name="app_article")
     */
    public function showArticle(UserInterface $user, $slug, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager -> getRepository(Article::class);
        /** @var Article $article */

        $article = $repository -> findOneBy(['slug' => $slug]);
        /* wyciagniecie author_id artykulu */


        $user_id = $user->getId();

        if (!$article) {
            throw $this->createNotFoundException(sprintf('Brak artykułu: "%s"', $slug));
        }
        return $this->render('article/article.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
            'user_id' => $user_id,
        ]);
    }
}
