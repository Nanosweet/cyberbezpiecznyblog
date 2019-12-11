<?php
/*
 * Kontroler odpowiedzialny za edytowanie artykułu */
namespace App\Controller\Article;

use App\Entity\Article;
use App\Form\EditArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleEditController extends AbstractController
{
    /**
     * @Route("/article/{slug}/edit", name="app_article_edit")
     * @IsGranted("ROLE_USER")
     */
    public function article_edit(Article $article, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(EditArticleFormType::class, $article);
        /*
         * Pobranie $slug do parametru RedirectToRoute*/
        $slug = $article->getSlug();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article', ['slug'=>$slug]);

        }

        return $this->render('article_edit/article_edit.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }
}
