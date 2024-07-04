<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('goalsScoredRight',TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пустое поле ввода'
                    ]),
                    new Length([
                        'min' => 0,
                        'max' => 255,
                        'minMessage' => 'Your first text must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your first text cannot be longer than {{ limit }} characters',
                    ])
                ]
            ])
            ->add('goalsScoredLeft', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пустое поле ввода'
                    ]),
                    new Length([
                        'min' => 0,
                        'max' => 255,
                        'minMessage' => 'Your first text must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your first text cannot be longer than {{ limit }} characters',
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
