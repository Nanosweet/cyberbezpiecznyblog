<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security")
     */
    public function _login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     *@Route("/rejestracja", name="app_rejestracja")
     */
    public function _register()
    {
        return $this->render('security/register.html.twig');
    }
}
