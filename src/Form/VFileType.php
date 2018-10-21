<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', CollectionType::class, [
                    'entry_type' => FileType::class,
                    'label' => 'false',
                    'entry_options' => [
                        'label' => false,
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'by_reference' => true,
                    'delete_empty' => true,
                    'attr' => [
                        'class' => 'form-control files-collection',
                    ],
                ])
                
            ->add('submit', SubmitType::class, array('label' => 'Guardar',
                    'attr' => array(
                        'class' => 'btn btn-vicsa-indigo col-auto'
                    )
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
