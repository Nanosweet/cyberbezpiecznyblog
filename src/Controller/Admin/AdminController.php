<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Likes;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(EntityManagerInterface $em)
    {
        /*
         * Article Repository
         */
        $articleRepository = $em->getRepository(Article::class);
        $articles = $articleRepository->findAllPublishedNonDeletedNonReported();
        $article = $articleRepository->findAllPublishedRecently();

        /*
         * User Repository
         */
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->findAll();
        /*
         * Comment Repository
         */
        $commentRepository = $em->getRepository(Comment::class);
        $comment = $commentRepository->findAllPublishedNonDeleted();
        /*
         * Like Repository
         */
        $likesRepository = $em->getRepository(Likes::class);
        $likes = $likesRepository->findAll();


        //dd($article);
        return $this->render('admin/admin.html.twig', [
            'user' => $user,
            'article' => $article,
            'articles' => $articles,
            'comment' => $comment,
            'likes' => $likes,
        ]);
    }
    /**
     * @Route("/show")
     */
    public function show(EntityManagerInterface $em)
    {
        $articleRepository = $em->getRepository(Article::class);
        $article = $articleRepository->findAllPublishedNonDeletedNonReported();

        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->findAll();

        $likeRepository = $em->getRepository(Likes::class);
        $like = $likeRepository->findAll();
        dd($like);
    }
}
