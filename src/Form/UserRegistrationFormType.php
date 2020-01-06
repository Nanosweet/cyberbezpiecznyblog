<?php

namespace App\Form;

use App\Entity\User;
use http\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imie', TextType::class, [
                'label' => 'Imię',
                'required' => true,
                /*'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Z][a-z]{3,15}$/',
                        'message' => 'Podaj swoje imię z wielkiej litery.'
                    ])
                ]*/
            ])
            ->add('nazwisko', TextType::class, [
                'label' => 'Nazwisko',
                'required' => true,
                /*'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Z][a-z]{3,25}$/',
                        'message' => 'Podaj swoje nazwisko z wielkiej litery.'
                    ])
                ]*/
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                /*'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9.!#$%&\'*+=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/'
                    ])
                ]*/
            ])
            ->add('password',  PasswordType::class, [
                'label' => 'Hasło',
                'required' => true,
                /*'constraints' =>  [
                    new Regex([
                        'pattern' => '/^(?=^.{4,}$)((?=.*\d)|(?=.*\W+))(?=.*[A-Z])(?=.*[a-z]).*$/',
                        'message' => 'Wprowadź małe, duże litery, cyfry oraz znaki specjalne, minimum 6 znaków.',
                    ])
                ]*/
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}
