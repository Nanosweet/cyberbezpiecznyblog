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
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\LikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{slug}", name="app_article")
     */
    public function article($slug, EntityManagerInterface $entityManager, Request $request, CommentRepository $commentRepository, LikesRepository $likesRepository)
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
            /*
            $repository = $entityManager->getRepository(Likes::class);
            $like = $repository->findAllLikedByUserID($user_id);
            if ($like != null)
            {
                $user_like = $like[0]->getUserID();
                $user_like_id = $user_like->getId();
            } else
                $user_like_id = null;
*/

            //dd($user_like_id);


            /*
             * Tworzenie komentarza */
            $comment = new Comment();

            /*
             * Pobranie id artykulu */
            $article->getId();
            $articleID = $article->getId();


            $likes1 = $likesRepository->findAllByArticleUserID($articleID, $user_id);
            $likes = count($likes1);
            //dd($likes);


            /*
             * Tworzenie formularza do komentowania */
            $form = $this->createForm(CommentCreateFormType::class, $comment);
            $form->handleRequest($request);

            /*
             * Warunek, sprawdzajacy formularz */
            if ($form->isSubmitted() && $form->isValid()) {

                /*
                 * Ustawienie autora artykulu */
                $comment
                        ->setAuthorFirstName($user_firstname)
                        ->setAuthorLastName($user_lastname)
                        ->setAuthor($user)
                        ->setIsDeleted(false)
                        ->setIsReported(false)
                        ->setArticle($article);

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

            $isLiked = is_null($likesRepository->findLikedPostByUser($article, $user)) ? 'false' : 'true';

            /*
             * Wyswietlenie widoku artykulu dla zalogowanego uzytkwonika */
            return $this->render('article/article.html.twig', [
                'article' => $article,
                'comments' => $comments,
                /*'user_like_id' => $user_like_id,*/
                'likes' => $likes,
                'user_id' => $user_id,
                'slug' => $slug,
                'commentForm' => $form->createView(),
                'isLiked' => $isLiked
            ]);
        }

        /*
         * Wyswietlanie widoku dla anonimowego uzytkownika */
        return $this->render('article/article_annonymous.html.twig', [
            'article' => $article,
            'slug' => $slug,
            /*'likes' => $likes,*/
            'comments' => $comments,
        ]);
    }
    /* DODAC ZE USER MUSI BYC ZALOGOWANY */
    /**
     * @Route("article/{id}/like", name="app_article_like")
     * @IsGranted("ROLE_USER")
     */
    public function article_like($id, ArticleRepository $articleRepository, EntityManagerInterface $em, LikesRepository $likesRepository)
    {
        $article = $articleRepository->find($id);
        $slug = $article->getSlug();
        $user = $this->getUser();

        $userID = $user->getId();

        $likes=$likesRepository->findAllLikedByUserID($userID);

        //dd(count($likes));


            $like = new Likes();

            $like
                ->setUserid($userID)
                ->setPostid($id)
                ;

            $article->incrementLikes();

            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();


    }

    /**
     * @Route("article/{id}/info")
     */
    public function info($id, ArticleRepository $articleRepository, EntityManagerInterface $em, LikesRepository $likesRepository)
    {
        $article = $articleRepository->find($id);
        $user = $this->getUser();
        $userID = $user->getId();

        dd($article);
    }

    /**
     * @Route("article/{id}/unlike", name="app_article_unlike")
     * @IsGranted("ROLE_USER")
     */
    public function article_unlike($id, ArticleRepository $articleRepository, EntityManagerInterface $em, LikesRepository $likesRepository)
    {
        $article = $articleRepository->find($id);
        $slug = $article->getSlug();
        $user = $this->getUser();
        $userID = $user->getId();

        $likes = $likesRepository->findAllByArticleUserID($id, $userID);
        $article->decrementLikes();


        foreach ($likes as $like) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($like);
            $em->flush();
        }



        $em->flush();
        dd($article);
    }
    /**
     * @Route("/article/{slug}/report", name="app_article_report")
     * @IsGranted("ROLE_USER")
     */
    public function article_report($slug,EntityManagerInterface $entityManager, Article $article, Request $request)
    {
        $article->setReported(true)
                ->setReportedAt(new \DateTime());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> persist($article);
        $entityManager -> flush();

        return $this->redirectToRoute("app_homepage");
    }

    /**
     * @Route("/comment/report", name="app_comment_report")
     */
    public function comment_report(EntityManagerInterface $entityManager, Request $request, ArticleRepository $articleRepository)
    {
        /*
         * Pobranie id komentarza do usuniecia
         * pobranie repozytorium
         */

        $commentID = $request->get('commentID');
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->find($commentID);
        $slug = $comment->getArticle()->getSlug();
        //dd($a);
        $comment
            ->setIsReported(true)
            ->setReportedAt(new \DateTime());


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_article', ['slug'=>$slug]);
    }


}
