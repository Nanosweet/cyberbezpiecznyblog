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
     * @Route("/login", name="app_login")
     */
    public function _login(AuthenticationUtils $authenticationUtils)
    {
        // Dodanie REMEMBER_ME
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('app_homepage'));
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(UserLoginFormType::class);

        return $this->render(
            'security/login.html.twig', [
                'error' => $error,
                'loginForm' => $form->createView()
        ]);
    }

    /**
     *@Route("/register", name="app_register")
     */
    public function _register(ValidatorInterface $validator, Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData(); #Pobranie danych z formularza

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');

        }

        return $this->render('security/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }

    /**
     * @Route ("/logout", name="app_logout")
     */
    public function _logout()
    {
        throw new \Exception("Do tego nie powinno dojść");
    }
}
