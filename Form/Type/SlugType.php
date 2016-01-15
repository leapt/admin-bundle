<?php

namespace Leapt\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SlugType
 *
 * @package Leapt\AdminBundle\Form\Type
 */
class SlugType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_slug';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['target'])
            ->setAllowedTypes('target', 'string');
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['target'] = $options['target'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }
}