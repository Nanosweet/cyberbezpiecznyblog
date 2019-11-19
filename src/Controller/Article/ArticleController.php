<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentCreateFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
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
    public function article($slug, EntityManagerInterface $entityManager, Request $request, CommentRepository $commentRepository)
    {
        /* Przypisanie repozytorium */
        $repository = $entityManager->getRepository(Article::class);
        /** @var Article $article */

        /* Wyszukanie artykułu po zmiennej slug, czyli slangu - tytule */
        $article = $repository->findOneBy(['slug' => $slug]);
        /* Pobranie komentarzy z artykułów */
       // $comments = $article->getComments();
        /* Wybranie komentarzy od najnowszego do najstarszego */
       // $comments = $commentRepository->findAllPublishedByNewest();

        /* Pobranie id zalogowanego usera */
        
        if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            /** @var User $user */
            $user = $this->getUser();
            $user_id = $user->getId();
            //$user_imie = $user->getImie();
            //$user_nazwisko = $user->getNazwisko();
            //dd($user_nazwisko);
            

            if (!$article) {
                throw $this->createNotFoundException(sprintf('Brak artykułu: "%s"', $slug));
            }

            /* Tworzenie komentarza */
            $comment = new Comment();

            $article ->getId();

            


            $form = $this->createForm(CommentCreateFormType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $comment ->setAuthorName($user_imie)
                         ->setAuthorForname($user_nazwisko)
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
               // 'comments' => $comments,
                'user_id' => $user_id,
                'commentForm' => $form->createView(),
            ]);
        }

        /* Wyświetlanie widoku dla anonimowego użytkownika */
        return $this->render('article/article_annonymous.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
            'comments' => $comments,
        ]);
    }


}
