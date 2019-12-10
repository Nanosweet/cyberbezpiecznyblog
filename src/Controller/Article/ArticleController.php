<?php
/*
 * Kontroler odpowiedzialny za wyświetlanie konkretnego artykułu
 * Kontroler odpowiedzialny za tworzenie komentarzy */
namespace App\Controller\Article;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\User;
use App\Form\CommentCreateFormType;
use App\Repository\CommentRepository;
use App\Repository\LikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{slug}", name="app_article")
     */
    public function article($slug, EntityManagerInterface $entityManager, Request $request, CommentRepository $commentRepository)
    {

        $repository = $entityManager->getRepository(Article::class);

        /** @var Article $article */
        $article = $repository->findOneBy(['slug' => $slug]);
        $likes = $article->getLikes();

        /*
         * Pobranie komentarzy
         * Wybranie komentarzy od najnowszego do najstarszego */
        $comments = $commentRepository->findAllPublishedByNewest();

        /*
         * Warunek, sprawdza czy istnieje odpowieni artykul */

        if (!$article) {
            throw $this->createNotFoundException(sprintf('Brak artykułu: "%s"', $slug));
        }

        /*
         * Warunek, sprawdza czy uzytkownik jest zalogowany
         * Jesli jest dodana funkcjonalnosc pisania komentarzy */
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {

            /*
             * $user - przypisanie zalogowanego użytkownika
             * $user_id - pobranie id zalogowanego użytkownika
             * $user_fullname - pobranie nazwy zalogowanego użytkownika */
            /** @var User $user */
            $user = $this->getUser();
            $user_id = $user->getId();
            $user_firstname = $user->getFirstname();
            $user_lastname = $user->getLastname();
            /*
             * Sprawdzam czy user polubil artykul
             * Dostosuje odpowiednie serduszko w twigu */
            $repository = $entityManager->getRepository(Likes::class);
            $like = $repository->findAllLikedByUserID($user_id);
            if ($like != null)
            {
                $user_like = $like[0]->getUserID();
                $user_like_id = $user_like->getId();
            } else
                $user_like_id = null;


            //dd($user_like_id);


            /*
             * Tworzenie komentarza */
            $comment = new Comment();

            /*
             * Pobranie id artykulu */
            $article->getId();

            /*
             * Tworzenie formularza do komentowania */
            $form = $this->createForm(CommentCreateFormType::class, $comment);
            $form->handleRequest($request);

            /*
             * Warunek, sprawdzajacy formularz */
            if ($form->isSubmitted() && $form->isValid()) {

                /*
                 * Ustawienie autora artykulu */
                $comment -> setAuthorFirstName($user_firstname)
                         -> setAuthorLastName($user_lastname)
                         -> setArticle($article);

                /*
                 * Pobranie danych z formularza */
                $comment = $form->getData();

                /*
                 * Wprowadzenie danych do bazy danych */
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirect($request->getUri());

            }


            /*
             * Wyswietlenie widoku artykulu dla zalogowanego uzytkwonika */
            return $this->render('article/article.html.twig', [
                'article' => $article,
                'comments' => $comments,
                'user_like_id' => $user_like_id,
                'likes' => $likes,
                'user_id' => $user_id,
                'slug' => $slug,
                'commentForm' => $form->createView(),
            ]);
        }

        /*
         * Wyswietlanie widoku dla anonimowego uzytkownika */
        return $this->render('article/article_annonymous.html.twig', [
            'article' => $article,
            'slug' => $slug,
            'likes' => $likes,
            'comments' => $comments,
        ]);
    }
    /* DODAC ZE USER MUSI BYC ZALOGOWANY */
    /**
     * @Route("article/{slug}/like", name="app_article_like")
     */
    public function article_like($slug, LoggerInterface $logger, EntityManagerInterface $entityManager, Article $article)
    {


        /** @var User $user */
        $user = $this->getUser();
        $user_id = $user->getId();
        $user_firstname = $user->getFirstname();
        $user_lastname = $user->getLastname();


        $article->incrementLikes();

        $likes = new Likes();

       $likes->setArticleID($article);
       $likes->setUserID($user);
       $likes->setCount($article->getLikes());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($likes);
        $entityManager->flush();
        $logger->info('Article is being hearted!');


        return new JsonResponse(['likes' => $article->getLikes()]);
    }
    /**
     * @Route("article/{slug}/unlike", name="app_article_unlike")
     */
    public function article_unlike($slug, LoggerInterface $logger, EntityManagerInterface $entityManager, Article $article)
    {
        /*
         * Pobranie user_id*/
        $user = $this->getUser();
        $user_id = $user->getId();

        /*
         * Pobranie LikesRepo
         * Zapytanie o polubienie przez zalogowanego usera */
        $repository = $entityManager->getRepository(Likes::class);
        $like = $repository->findAllLikedByUserID($user_id);
        $tablica = $like[0];


        $article->decrementLikes();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($tablica);
        $entityManager->flush();
        $logger->info('Article is being hearted!');


        return new JsonResponse(['likes' => $article->getLikes()]);
    }
    /**
     * @Route("/article/{slug}/report", name="app_article_report")
     */
    public function article_report($slug,EntityManagerInterface $entityManager, Article $article, Request $request)
    {
        $article->setReported(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> persist($article);
        $entityManager -> flush();

        return $this->redirectToRoute("app_homepage");
    }


}
