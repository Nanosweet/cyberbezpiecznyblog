<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginFormType;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{


    /**
     * @Route ("/logout", name="app_logout")
     */
    public function _logout()
    {
        throw new \Exception("Do tego nie powinno dojść");
    }
}
