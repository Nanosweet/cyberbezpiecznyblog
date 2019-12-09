<?php

namespace App\Controller\Account;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     */
    public function account(EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($this->getUser());
        //$user = $this->getUser();
        $user_fullname = $user->getId();
        //dd($user);

        return $this->render('account/account.html.twig', [
            'controller_name' => 'AccountController',
            'user' => $user
        ]);
    }
}
