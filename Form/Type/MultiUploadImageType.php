<?php

namespace Leapt\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MultiUploadImageType
 * @package Leapt\AdminBundle\Form\Type
 */
class MultiUploadImageType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_multiupload_image';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['im_resize'] = $options['im_resize'];
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'im_resize' => '200x',
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }
}
