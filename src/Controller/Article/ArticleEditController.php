<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\EditArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleEditController extends AbstractController
{
    /**
     * @Route("/article/{slug}/edit", name="app_article_edit")
     */
    public function editArticle(Article $article, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(EditArticleFormType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');

        }

        return $this->render('article_edit/article_edit.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }
}
