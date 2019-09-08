<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function _login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     *@Route("/register", name="app_register")
     */
    public function _register()
    {
        return $this->render('security/register.html.twig');
    }
}
