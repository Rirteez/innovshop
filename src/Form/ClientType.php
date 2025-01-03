<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adresse',
                    'class' => 'mgt-1 input-adresse',
                ],
            ])
            ->add('complement_adr', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Complément d\'adresse',
                    'class' => 'mgt-1 input-adresse',
                ],
            ])
            ->add('CP', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Code Postal',
                    'class' => 'mgt-1 input-adresse',
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'ville',
                    'class' => 'mgt-1 input-adresse',
                ],
            ])
            ->add('pays', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Pays',
                    'class' => 'mgt-1 input-adresse',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
