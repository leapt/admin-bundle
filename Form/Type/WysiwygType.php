<?php

namespace Leapt\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WysiwygType
 * @package Leapt\AdminBundle\Form\Type
 */
class WysiwygType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_wysiwyg';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'wysiwyg_config' => '/bundles/leaptadmin/js/ckeditor_config.js'
            ])
            ->setAllowedTypes('wysiwyg_config', 'string');
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['wysiwyg_config'] = $options['wysiwyg_config'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextareaType::class;
    }
}