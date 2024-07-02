<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityControllerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'Имя пользователя',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your username'
                    ])
                ]
            ])
            ->add('_password', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ])
                ],
            ])
            ->add('app_main', SubmitType::class, [
                'label' => 'Войти',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver
            ->setDefaults([
                'csrf_protection' => true
            ]);
    }
    public function getBlockPrefix()
    {
        return "";
    }
}
