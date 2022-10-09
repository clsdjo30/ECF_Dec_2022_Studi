<?php

namespace App\Form;

use App\Entity\PartnerPermission;
use App\Entity\Permission;
use App\Repository\PartnerPermissionRepository;
use App\Repository\PartnerRepository;
use App\Repository\PermissionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerPermissionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('permission')
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
            'data_class' => PartnerPermission::class,
        ]);
    }
}
