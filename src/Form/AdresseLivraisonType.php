<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdresseLivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'N° et nom de la rue',
                    'class' => 'mgt-1 input-adresse',
                ],
            ])
            ->add('complement_adr', TextType::class, [
                'label' => false,
                'required' => false,
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your default values here
        ]);
    }
}


?>