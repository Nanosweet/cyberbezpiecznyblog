<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\AccountChangeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     */
    public function account(EntityManagerInterface $entityManager, Request $request)
    {
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($this->getUser());

        return $this->render('account/account.html.twig', [
            'user' => $user
        ]);
    }
    /**
     * @Route("/account/change", name="app_account_change")
     */
    public function account_change(EntityManagerInterface $entityManager, Request $request)
    {
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($this->getUser());

        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');


        $user
            ->setFirstname($firstname)
            ->setLastname($lastname);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

        return $this->redirectToRoute("app_account");
    }
}
