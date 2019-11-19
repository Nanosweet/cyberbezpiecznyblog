<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Form\ArticleCreateFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleCreateController extends AbstractController
{
    /**
     * @Route("/create", name="app_article_create")
     * @IsGranted("ROLE_USER")
     */
    public function article_create(Request $request)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $form = $this->createForm(ArticleCreateFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('article_create/article_create.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }
}
