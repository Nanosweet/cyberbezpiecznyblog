<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\CreateArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreateArticleController extends AbstractController
{
    /**
     * @Route("/create/article", name="create_article")
     */
    public function createArticle(Request $request, EntityManagerInterface $entityManager)
    {
        $article = new Article();

        $form = $this->createForm(CreateArticleFormType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();
            $article = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_register');
        }

        return $this->render('create_article/index.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }
}
