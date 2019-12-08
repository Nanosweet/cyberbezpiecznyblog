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
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
            $user_fullname = $user->getFullname();

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
                $comment->setAuthorName($user_fullname)
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


            /*
             * Wyswietlenie widoku artykulu dla zalogowanego uzytkwonika */
            return $this->render('article/article.html.twig', [
                'article' => $article,
                'comments' => $comments,
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
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("article/{slug}/like", name="app_article_like")
     */
    public function article_like($slug, LoggerInterface $logger, EntityManagerInterface $entityManager, Article $article)
    {
        $article->incrementLikes();
        $entityManager->flush();
        $logger->info('Article is being hearted!');


        return new JsonResponse(['likes' => $article->getLikes()]);
    }
    /**
     * @Route("article/{slug}/unlike", name="app_article_unlike")
     */
    public function article_unlike($slug, LoggerInterface $logger, EntityManagerInterface $entityManager, Article $article)
    {
        $article->decrementLikes();
        $entityManager->flush();
        $logger->info('Article is being hearted!');


        return new JsonResponse(['likes' => $article->getLikes()]);
    }


}
