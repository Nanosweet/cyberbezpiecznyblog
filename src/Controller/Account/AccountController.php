<?php

namespace App\Controller\Account;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     */
    public function account(EntityManagerInterface $entityManager, ArticleRepository $articleRepository, CommentRepository $commentRepository)
    {
        /*
         * Pobranie User i UserID
         */
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($this->getUser());
        $userID = $user->getId();

        /*
         * Wybranie artykułów autorstwa usera
         */
        $article = $articleRepository->findAllPublishedByUser($userID);

        /*
         * Wybreanie artykułów skomentowanych przez usera
         */
        $comment = $commentRepository->findAllCommentedByUser($userID);

        /*
         * Renderowanie widoku
         */
        return $this->render('account/account.html.twig', [
            'user' => $user,
            'article' => $article,
            'comment' => $comment,
        ]);
    }
    /**
     * @Route("/account/change", name="app_account_change")
     */
    public function account_change(EntityManagerInterface $entityManager, Request $request)
    {
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($this->getUser());

        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');


        $user
            ->setFirstname($firstname)
            ->setLastname($lastname);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

        return $this->redirectToRoute("app_account");
    }

    /**
     * @Route("/account/comment/delete", name="app_comment_delete")
     */
    public function account_comment_delete(EntityManagerInterface $entityManager, Request $request)
    {
        /*
         * Pobranie id komentarza do usuniecia
         * pobranie repozytorium
         * query
         */
        $commentID = $request->get('commentID');
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->find($commentID);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();


        return $this->redirectToRoute("app_account");
    }
    /**
     * @Route("/account/article/delete", name="app_article_delete")
     */
    public function article_delete(EntityManagerInterface $entityManager, Request $request)
    {

        $repository = $entityManager->getRepository(Article::class);

        /*
         * Pobranie id artykułu i wyszukanie go po id
         */
        $articleID = $request->get('articleID');
        $article = $repository->find($articleID);
        $article
            ->setIsDeleted(true);


        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($article);

        $entityManager->flush();

        return $this->redirectToRoute("app_account");


    }
}
