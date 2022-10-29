<?php

namespace App\Form;

use App\Entity\Permission;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'data_class' => Permission::class,
                'choice_attr' => function(?Permission $permission) {
                    return  $permission ? [
                        'class' => 'permission_'.strtolower($permission->getName())
                    ] : [];
                }
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'role' => 'switch'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
        ]);
    }
}
