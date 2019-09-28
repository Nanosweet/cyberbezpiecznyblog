<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreateArticleController extends AbstractController
{
    /**
     * @Route("/create/article", name="create_article")
     */
    public function index()
    {
        return $this->render('create_article/index.html.twig', [
            'controller_name' => 'CreateArticleController',
        ]);
    }
}
