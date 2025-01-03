<?php

namespace App\Form;

use App\Entity\Client; //
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajouter les champs nécessaires au formulaire
        $builder
            ->add('prenom', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prenom',
                    'class' => 'mgt-1 input-login-register',
                    ],
            ])
            ->add('nom', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'mgt-1 input-login-register',
                    ],
            ])
            ->add('mail', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adresse mail',
                    'class' => 'mgt-1 input-login-register',
                    ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'required' => true,
                'first_options'  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                        'class' => 'mgt-1 input-login-register',
                        ],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Confirmer le mot de passe',
                        'class' => 'mgt-1 input-login-register',
                    ],
                ],
            ]);
            if ($options['add_submit']) {
                $builder->add('submit', SubmitType::class, [
                    'label' => 'Créer mon compte',
                    'attr' => ['class' => 'button button--primary mgt-2'],
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'add_submit' => true,
        ]);
    }
}


?>
