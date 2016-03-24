<?php

namespace Leapt\AdminBundle\Form\Type;

use Leapt\AdminBundle\Form\EventListener\MultiUploadSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MultiUploadType
 * @package Leapt\AdminBundle\Form\Type
 */
class MultiUploadType extends AbstractType
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * Constructor
     *
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new MultiUploadSubscriber($this->rootDir, $options['dst_dir']));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['entry_type'] = $options['entry_type'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['dst_dir'])
            ->setAllowedTypes('dst_dir', ['string', 'callable'])
            ->setDefaults([
                    'entry_type' => MultiUploadUrlType::class,
                ]
            );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_multiupload';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return CollectionType::class;
    }
}
