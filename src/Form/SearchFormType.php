<?php

namespace App\Form;

use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('q', TextType::class, [
               'label' => false,
               'required' => false,
               'attr' => [
                   'placeholder' => "Saisissez votre recherche"
               ],

           ])
           ->add('active', CheckboxType::class, [
               'label' => 'Franchisé actif',
               'required' => false,
           ])
           ->add('close', CheckboxType::class, [
               'label' => 'Franchisé inactif',
               'required' => false,
           ])
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }


    public function getBlockPrefix(): string
    {
        return '';
    }


}