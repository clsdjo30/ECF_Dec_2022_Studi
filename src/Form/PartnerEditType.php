<?php

namespace App\Form;

use App\Entity\Partner;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PartnerEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('phoneNumber', TextType::class)
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'role' => 'switch'
                ]
            ])
            ->add('user', UserEditType::class, [
                'data_class' => User::class,
            ])
            ->add('globalPermissions', CollectionType::class, [
                'entry_type' => PartnerPermissionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=> false,
            ])

           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
        ]);
    }
}
