<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditMovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imdb_id', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9
                ]
            ])
            ->add('error_audio', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'On' => true,
                    'Off' => false
                ],
            ])
        ;
    }
}
