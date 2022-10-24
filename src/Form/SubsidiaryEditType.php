<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\Subsidiary;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SubsidiaryEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la salle'
            ])
            ->add('url')
            ->add('logo', FileType::class, [
                'allow_extra_fields' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Téléchargez votre logo au format jpg ou PNG',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Merci de téléchargez une image valide',
                    ]),
                ],
            ])
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
            ->add('user', UserType::class, [
                'data_class' => User::class
            ])
            ->add('subsidiaryPermissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'name'
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
