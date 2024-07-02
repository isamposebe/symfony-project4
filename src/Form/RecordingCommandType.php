<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RecordingCommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Ведите наименование команды',
                'required' => true,
                'attr' => ['class' => 'nameTeam'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a field name Team',
                    ]),
                ]])
            ->add('button', ButtonType::class, [
                'label' => 'Записать команду',
                'attr' => ['class' => 'recordingTeam']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
