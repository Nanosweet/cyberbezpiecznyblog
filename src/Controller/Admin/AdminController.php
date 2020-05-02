<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\User;
use App\Form\CommentEditFormType;
use App\Form\EditArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function MongoDB\Driver\Monitoring\addSubscriber;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(ArticleRepository $articleRepository, UserRepository $userRepository, CommentRepository $commentRepository, LikesRepository $likesRepository)
    {
        /*
         * Wybranie artykułów, które nie są ani usunięte, ani zgłoszone
         *
         * Funkcjonalność -  Wyswietlenie ilości wszystkich artykułów
         */
        $articles = $articleRepository->findAllPublishedNonDeletedNonReported();

        /*
         * Wybranie ostatnio dodanych artykułów
         * Funkcjonalność wyświetlenia listy ostatnio dodanych artykułów
         */
        $article = $articleRepository->findAllPublishedRecently();

        /*
         * Wybranie wszystkich użytkowników
         * Funkcjonalność wyświetlenia liczby wszystkich użytkowników
         */
        $user = $userRepository->findAll();

        /*
         * Wybranie wszystkich nie usuniętych komentarzy
         * Funkcjonalność wyswietlenia ilości wszystkich komentarzy
         */
        $comment = $commentRepository->findAllPublishedNonDeletedNonReported();

        /*
         * Wybranie wszystkich polubień
         * Funkcjonalność wyswietlenia ilości wszystkich polubień
         */
        $likes = $likesRepository->findAll();

        /*
         * Renderowanie widoku
         * Ustawienie zmiennch do Twig
         */
        return $this->render('admin/admin.html.twig', [
            'user' => $user,
            'article' => $article,
            'articles' => $articles,
            'comment' => $comment,
            'likes' => $likes,
        ]);
    }

    /*
     * Funkcjonalność - Mozliwosc usuniecia dowolnego uzytkownika
     */
    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete")
     */
    public function admin_delete_user($id, EntityManagerInterface $entityManager, LikesRepository $likesRepository, UserRepository $userRepository, CommentRepository $commentRepository, ArticleRepository $articleRepository)
    {
        $user_id = $id;

        $userArticles = $articleRepository->findAllPublishedByUser($user_id);
        $userComments = $commentRepository->findAllCommentedByUser($user_id);
        $userLikes = $likesRepository->findAllLikedByUserID($user_id);


        $user = $userRepository->findOneBy(['id' => $user_id]);


        if (count($userArticles) != 0) {
            foreach ($userArticles as $article) {

                $articleID = $article->getId();
                $comments = $commentRepository->findAllByArticleID($articleID);

                if (count($comments) != 0) {
                    foreach ($comments as $comment) {

                        $entityManager->remove($comment);
                        $entityManager->flush();
                    }
                } elseif (count($userComments) != 0) {
                    foreach ($userComments as $comments) {

                        $entityManager->remove($comments);
                        $entityManager->flush();
                    }
                } elseif (count($userLikes) != 0) {
                    foreach ($userLikes as $likes) {

                        $article_id = $likes->getPostid();
                        $article = $articleRepository->find($article_id);

                            $article->decrementLikes();



                        $entityManager->remove($likes);
                        $entityManager->flush();
                    }
                }

                $entityManager->remove($article);
                $entityManager->flush();
            }
        } elseif (count($userComments) != 0) {
            foreach ($userComments as $comments) {

                $entityManager->remove($comments);
                $entityManager->flush();
            }
        } elseif (count($userLikes) != 0) {
            foreach ($userLikes as $likes) {

                $article_id = $likes->getPostid();
                $article = $articleRepository->find($article_id);
                

                     $article->decrementLikes();




                $entityManager->remove($likes);
                $entityManager->flush();
            }
        }

        $entityManager->remove($user);
        $entityManager->flush();


        return $this->redirectToRoute('admin_users_all');

    }

    /*
     * Funkcjonalność - Mozliwosc usuniecia dowolnego artykulu
     */
    /**
     * @Route("/admin/articles/all/delete", name="admin_articles_all_delete")
     */
    public function articles_all_delete(Request $request, ArticleRepository $articleRepository)
    {
        /*
         * Pobranie id artykułu z input
         * Wybranie  z bazy danych tego artykułu
         * Ustawienie isDeleted na true
         */
        $articleID = $request->get('articleID');
        $article = $articleRepository->find($articleID);
        $article
            ->setIsDeleted(true)
            ->setDeletedAt(new \DateTime());

        /*
         * Wprowadzenie zmian do bazy danych
         */
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        /*
         * Renderowanie widoku
         * Powrót do /admin/articles/all
         */
        return $this->redirectToRoute("admin_articles_all");
    }

    /*
     * Funkcjonalność - Mozliwosc usuniecia dowolnego komentarza
     */
    /**
     * @Route("/admin/comments/all/delete", name="admin_comments_all_delete")
     */
    public function admin_comment_delete(EntityManagerInterface $entityManager, Request $request)
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


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute("admin_comments_all");
    }

    /*
     * Funkcjonalność - Mozliwosc usunięcia zgłoszonego artykulu
     */
    /**
     * @Route("/admin/articles/reported/delete", name="admin_articles_reported_delete")
     */
    public function admin_articles_reported_delete(EntityManagerInterface $entityManager, Request $request, ArticleRepository $articleRepository)
    {
        /*
         * Pobranie id artykułu z request
         * Wybranie  z bazy danych tego artykułu
         * Ustawienie isDeleted na true
         */
        $articleID = $request->get('articleID');
        $article = $articleRepository->find($articleID);
        $article
            ->setIsDeleted(true);

        /*
         * Wprowadzenie zmian do bazy danych
         */
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        /*
         * Renderowanie widoku
         * Powrót do /admin/articles/reported
         */
        return $this->redirectToRoute("admin_articles_reported");
    }

    /*
     * Funkcjonalość - Mozliwosc ponownej publikacji zgloszonego artykulu
     */
    /**
     * @Route("/admin/articles/{slug}/unreport", name="admin_articles_unreport")
     */
    public function article_unreport($slug, EntityManagerInterface $entityManager, Article $article, Request $request)
    {
        $article->setReported(false);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute("admin_articles_reported");
    }

    /*
     * Funkcjonalnosc - Mozliwosc ponownej publikacji zgloszonego komentarza
     */
    /**
     * @Route("/admin/comments/{id}/unreport", name="admin_comments_unreport")
     */
    public function comment_unreport($id, EntityManagerInterface $entityManager, Comment $comment, Request $request)
    {
        $comment->setIsReported(false);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('admin_comments_reported');
    }

    /*
     * Funkcjonalność - Mozliwosc usuniecia zgloszonego komentarza
     */
    /**
     * @Route("/admin/comments/reported/delete", name="admin_comments_reported_delete")
     */
    public function admin_comments_reported_delete(EntityManagerInterface $entityManager, Request $request)
    {
        /*
         * Pobranie id komentarza do usuniecia
         * pobranie repozytorium
         */

        $commentID = $request->get('commentID');
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->find($commentID);
        $comment
            ->setIsDeleted(true)
            ->setDeletedAt(new \DateTime());


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute("admin_comments_reported");
    }

    /*
     * Funkcjonalność - Mozliwosc edycji dowolnego artykulu
     */
    /**
     * @Route("/admin/articles/edit/{slug}", name="admin_articles_edit")
     */
    public function articles_edit(Article $article, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(EditArticleFormType::class, $article);
        /*
         * Pobranie $slug do parametru RedirectToRoute */

        $slug = $article->getSlug();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_articles_all');

        }

        return $this->render('article_edit/article_edit.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }


    /*
     * Funkcjonalność - Mozliwosc edycji dowolnego komentarza
     */
    /**
     * @Route("/admin/comments/edit", name="admin_comments_edit")
     */
    public function admin_comments_edit(EntityManagerInterface $entityManager, Request $request)
    {
        $commentID = $request->get('commentID');

        $commentRepository = $entityManager->getRepository(Comment::class);
        $_comment = $commentRepository->findOneBy(['id' => $commentID]);

        $form = $this->createForm(CommentEditFormType::class, $_comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();

            $entityManager->persist($comment);
            $entityManager->flush();


            return $this->redirectToRoute('admin_comments_all');
        }

        return $this->render('article_comment_edit/article_comment_edit.html.twig', [
            'comment' => $_comment,
            'editForm' => $form->createView()
        ]);
    }

    /*
     * Funkcjonalność - Wyświetlanie zgłoszonych artykułów
     */

    /**
     * @Route("/admin/articles/reported", name="admin_articles_reported")
     */
    public function admin_articles_reported(ArticleRepository $articleRepository)
    {
        /*
         * Wybranie wyłącznie zgłoszonych artykułów
         */
        $article = $articleRepository->findAllArticlesReported();

        /*
         * Renderowanie wiodku
         * Ustawienie zmiennych do Twig
         */
        return $this->render('admin/articles_reported.html.twig', [
            'article' => $article,
        ]);
    }

    /*
     * Funkcjonalność - Wyświetlenie usuniętych artykułów
     */

    /**
     * @Route("/admin/articles/deleted", name="admin_articles_deleted")
     */
    public function admin_articles_deleted(ArticleRepository $articleRepository)
    {
        $article = $articleRepository->findAllDeleted();

        return $this->render('admin/articles_deleted.html.twig', [
            'article' => $article,
        ]);
    }

    /*
     * Funkcjonalność - Wyswietlenie listy wszystkich artykułów
     */

    /**
     * @Route("/admin/articles/all", name="admin_articles_all")
     */
    public function admin_articles_all(ArticleRepository $articleRepository)
    {
        /*
         * Pobranie wszystkich artykułów z bazy danych
         */
        $article = $articleRepository->findAllPublishedAdmin();

        /*
         * Renderowanie widoku
         * Ustawienie zmiennych do Twig
         */
        return $this->render('admin/articles_all.html.twig', [
            'article' => $article,
        ]);
    }

    /*
     * Funkcjonalność - Edycja zgłoszonego artykułu
     */

    /**
     * @Route("/admin/articles/reported/edit/{slug}", name="admin_articles_reported_edit")
     */
    public function articles_reported_edit(Article $article, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(EditArticleFormType::class, $article);
        /*
         * Pobranie $slug do parametru RedirectToRoute*/
        $slug = $article->getSlug();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_articles_reported');

        }


        return $this->render('article_edit/article_edit.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    // KOMENTARZE

    /*
     * Funkcjonalność - Wyświetlenie listy wszystkich komentarzy
     */

    /**
     * @Route("/admin/comments/all", name="admin_comments_all")
     */
    public function comments_all(CommentRepository $commentRepository)
    {
        $comment = $commentRepository->findAllPublishedNonDeletedNonReported();

        return $this->render('admin/comments_all.html.twig', [
            'comment' => $comment,
        ]);
    }

    /*
     * Funkcjonalność - Wyświetlenie listy zgłoszonych komentarzy
     */

    /**
     * @Route("/admin/comments/reported", name="admin_comments_reported")
     */
    public function comments_reported(CommentRepository $commentRepository)
    {
        $comment = $commentRepository->findAllReported();

        return $this->render('admin/comments_reported.html.twig', [
            'comment' => $comment,
        ]);
    }

    /*
     * Funkcjonalność - Wyświetlenie listy usuniętych komentarzy
     */
    /**
     * @Route("/admin/comments/deleted", name="admin_comments_deleted")
     */
    public function comments_deleted(CommentRepository $commentRepository)
    {
        $comment = $commentRepository->findAllDeleted();

        return $this->render('admin/comments_deleted.html.twig', [
            'comment' => $comment,
        ]);
    }

    /*
     * Funkcjonalność - Wyświetlenie listy wszystkich użytkowników w panelu administratora
     */
    /**
     * @Route("/admin/users/all", name="admin_users_all")
     */
    public function admin_users_all(UserRepository $userRepository)
    {
        $user = $userRepository->findAll();

        return $this->render('admin/users_all.html.twig', [
            'user' => $user
        ]);
    }
}
