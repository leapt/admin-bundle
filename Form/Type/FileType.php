<?php

namespace Leapt\AdminBundle\Form\Type;

use Leapt\AdminBundle\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FileType
 *
 * Used in the wysiwyg file browser
 *
 * @package Leapt\AdminBundle\Form\Type
 */
class FileType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'admin_leapt_file';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', \Symfony\Component\Form\Extension\Core\Type\FileType::class)
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'wysiwyg.upload.placeholder.name'],
            ])
            ->add('tags', TextType::class, [
                'attr' => ['placeholder' => 'wysiwyg.upload.placeholder.tags'],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => File::class,
            'translation_domain' => 'LeaptAdminBundle',
        ]);
    }
}
