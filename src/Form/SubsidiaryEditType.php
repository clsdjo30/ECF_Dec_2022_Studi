<?php

namespace App\Form;

use App\Entity\Subsidiary;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubsidiaryEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la salle'
            ])
            ->add('url')
            ->add('description', TextareaType::class)
            ->add('phoneNumber', TextType::class )
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('postalCode', TextType::class)
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'role' => 'switch'
                ]
            ])
            ->add('user', UserEditType::class, [
                'data_class' => User::class
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subsidiary::class,
        ]);
    }
}
