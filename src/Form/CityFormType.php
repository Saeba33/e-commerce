<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'label' => 'Nom de la ville',
                'attr' => ['class' => 'form form-control', 'placeholder' => 'Nom de la ville']
            ])
            ->add('shipping_cost', null, [
                'required' => true,
                'label' => 'Frais de livraison',
                'attr' => ['class' => 'form form-control', 'placeholder' => 'Frais de livraison']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}