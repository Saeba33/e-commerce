<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price');
            
        // Ajouter le champ stock seulement en mode création, pas en édition
        if (!$options['is_edit']) {
            $builder->add('stock', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le stock ne peut pas être vide.']),
                    new PositiveOrZero(['message' => 'Le stock doit être positif ou égal à zéro.'])
                ],
                'attr' => [
                    'min' => 0
                ]
            ]);
        } else {
            // En mode édition, on rend le champ non mappé pour éviter les erreurs
            $builder->add('stock', IntegerType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'disabled' => true
                ]
            ]);
        }
        
        $builder
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                            'image/webp',
                        ],
                        'maxSizeMessage' => 'Votre image ne doit pas dépasser 1024 ko',
                        'mimeTypesMessage' => 'Veuillez choisir un format valide (jpeg, png, jpg, webp)',
                    ])
                ]
            ])
            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'is_edit' => false,
        ]);
    }
}
