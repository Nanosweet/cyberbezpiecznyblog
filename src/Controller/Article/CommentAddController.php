<?php

namespace App\Controller\Article;

use App\Entity\Comment;
use App\Form\CommentCreateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentAddController extends AbstractController
{
    /**
     * @Route("article/{slug}/comment", name="app_article_comment_add")
     */
    public function article_comment(Request $request)
{
    $comment = new Comment();
    $comment->setAuthorName('name od autora');
dd($comment);
    $form = $this->createForm(CommentCreateFormType::class, $comment);

    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {

        $form->getData();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_homepage');
    }

    return $this->render('comment_add/article_search.html.twig', [
        'articleForm' => $form->createView(),
    ]);
}
}
