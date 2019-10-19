<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentCreateFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{slug}", name="app_article")
     */
    public function showArticle($slug, EntityManagerInterface $entityManager, Request $request)
    {
        $repository = $entityManager->getRepository(Article::class);
        /** @var Article $article */

        $article = $repository->findOneBy(['slug' => $slug]);
        $comments = $article->getComments();
        /* wyciagniecie id zalogowanego usera */
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            /** @var User $user */
            $user = $this->getUser();
            $user_id = $user->getId();

            if (!$article) {
                throw $this->createNotFoundException(sprintf('Brak artykułu: "%s"', $slug));
            }

            $comment = new Comment();

            $article ->getId();


            $form = $this->createForm(CommentCreateFormType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $comment ->setAuthorName('Janek')
                         ->setArticle($article);
                $comment = $form->getData();
                //dd($comment);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
            }


            return $this->render('article/article.html.twig', [
                'controller_name' => 'ArticleController',
                'article' => $article,
                'comments' => $comments,
                'user_id' => $user_id,
                'commentForm' => $form->createView(),
            ]);
        }

        return $this->render('article/article_annonymous.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
            'comments' => $comments,
        ]);
    }


}
