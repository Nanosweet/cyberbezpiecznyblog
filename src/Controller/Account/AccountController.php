<?php

namespace App\Controller\Account;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\User;
use App\Form\CommentEditFormType;
use App\Form\EditArticleFormType;
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
    /**
     * @Route("/aa/account/comment/edit/")
     */
    public function comment_edit(EntityManagerInterface $em, Request $request, Comment $comment)
    {
        $form = $this->createForm(CommentEditFormType::class);
        $form->handleRequest($request);

        $comment = $em->getRepository(Comment::class);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Comment $comment */
            $comment = $form->getData();
            dd($comment);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Article Created! Knowledge is power!');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('article_comments_edit/article_comments.html.twig', [
            'editForm' => $form->createView()
        ]);

    }
    /**
     * @Route("/account/comment/edit", name="app_comment_edit")
     */
    public function comment_edition(EntityManagerInterface $entityManager, Request $request)
    {
        $commentID = $request->get('commentID');

        $commentRepository = $entityManager->getRepository(Comment::class);
        $_comment = $commentRepository->findOneBy(['id'=>$commentID]);

        $form = $this->createForm(CommentEditFormType::class, $_comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Comment $comment */
            $comment = $form->getData();

            $entityManager->persist($comment);
            $entityManager->flush();


            return $this->redirectToRoute('app_account');
        }

        return $this->render('article_comment_edit/article_comment_edit.html.twig', [
            'comment' => $_comment,
            'editForm' => $form->createView()
        ]);







    }
}
