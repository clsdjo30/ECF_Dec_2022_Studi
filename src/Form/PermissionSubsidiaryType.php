<?php

namespace App\Form;

use App\Entity\PartnerPermission;
use App\Entity\SubsidiaryPermission;
use App\Repository\PartnerPermissionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionSubsidiaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partnerPermission', EntityType::class, [
                'class' => PartnerPermission::class,
                'query_builder' => function(PartnerPermissionRepository $repository) {
                return $repository->findOneBy(['permission' => 'permission.name']);
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
            'data_class' => SubsidiaryPermission::class,
        ]);
    }
}
