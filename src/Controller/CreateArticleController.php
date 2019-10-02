<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\CreateArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreateArticleController extends AbstractController
{
    /**
     * @Route("/create/article", name="app_create_article")
     * @IsGranted("ROLE_USER")
     */
    public function createArticle(Request $request, EntityManagerInterface $entityManager)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $form = $this->createForm(CreateArticleFormType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $article = $form->getData();



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('create_article/create_article.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }
}
