<?php

namespace App\Form;

use App\Entity\Participants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ParticipantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('niveauEtude', TextType::class, [
                'label' => 'Niveau etude',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])

            ->add('niveauEtude', ChoiceType::class, [
                'label' => "Niveau d'/étude",
                'placeholder' => "Niveau d'étude",
                'choices'  => [
                    'Académique' => 'Académique',
                    'Professionnel' => 'Professionnel',
                ],
                'expanded' => false,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}
