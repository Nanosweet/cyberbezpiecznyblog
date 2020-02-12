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
use Symfony\Component\Validator\Constraints\Date;

class AccountController extends AbstractController
{
    /*
     * Funkcjonalność - Wyświetlenie szczegółów użytkownika
     */

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
         * Wybranie artykułów autorstwa użytkownika
         */
        $article = $articleRepository->findAllPublishedByUserNonDeletedNonReported($userID);

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

    /*
     * Funkcjonlaność - Zmiana danych użytkownika
     */

    /**
     * @Route("/account/change", name="app_account_change")
     */
    public function account_change(EntityManagerInterface $entityManager, Request $request)
    {
        /*
         * Pobranie użytkownika
         */
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($this->getUser());

        /*
         * Pobranie danych z formularza
         */
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');


        /*
         * Wprowadzenie zmian do bazy danych
         */
        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
        ;

        /*
         * Zapisanie nowych danych do bazy danych
         */
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        /*
         * Powrót do widoku /account
         */
        return $this->redirectToRoute("app_account");
    }

    /*
     * Funkcjonalność - Usunięcie własnego komentarza
     */

    /**
     * @Route("/account/comment/delete/{slug}", name="app_comment_delete")
     */
    public function account_comment_delete(EntityManagerInterface $entityManager, Request $request, $slug)
    {
        /*
         * Pobranie id komentarza do usuniecia
         * pobranie repozytorium
         * query
         */
        $commentID = $request->get('commentID');
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->find($commentID);
        $comment
            ->setIsDeleted(true)
            ->setDeletedAt(new \DateTime());


        /*
         * Zapisanie zmian do bazy danych
         */
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        /*
         * Przekierowanie do /account
         */
        return $this->redirectToRoute('app_article', ['slug'=>$slug]);
    }

    /*
     * Funkcjonalność - Usuniecie wlasnego artykułu
     */

    /**
     * @Route("/account/article/delete", name="app_article_delete")
     */
    public function article_delete(EntityManagerInterface $entityManager, Request $request)
    {
        /*
         * Pobranie repozytorium klasy Article
         */
        $repository = $entityManager->getRepository(Article::class);

        /*
         * Pobranie id artykułu z formularza
         * Wyszukanie artykulu po id
         */
        $articleID = $request->get('articleID');
        $article = $repository->find($articleID);
        $article
            ->setIsDeleted(true)
            ->setDeletedAt(new \DateTime());


        /*
         * Zapisanie zmian do bazy danych
         */
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        /*
         * Przekierowanie do /account
         */
        return $this->redirectToRoute("app_account");
    }

    /*
     * Funkcjonalność - Edycja komentarzab
     */

    /**
     * @Route("/account/comment/edit", name="app_comment_edit")
     */
    public function comment_edition(EntityManagerInterface $entityManager, Request $request)
    {
        /*
         * Pobranie id komenatrza z formularza
         */
        $commentID = $request->get('commentID');

        /*
         * Wyszukanie komentarza z bazy
         */
        $commentRepository = $entityManager->getRepository(Comment::class);
        $_comment = $commentRepository->findOneBy(['id'=>$commentID]);

        /*
         * Tworzenie formularza
         */
        $form = $this->createForm(CommentEditFormType::class, $_comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Comment $comment */
            $comment = $form->getData();

            $entityManager->persist($comment);
            $entityManager->flush();


            /*
             * Przekierowanie do /account
             */
            return $this->redirectToRoute('app_account');
        }

        /*
         * Renderowanie widoku edycji komentarza
         */
        return $this->render('article_comment_edit/article_comment_edit.html.twig', [
            'comment' => $_comment,
            'editForm' => $form->createView()
        ]);
    }
}
