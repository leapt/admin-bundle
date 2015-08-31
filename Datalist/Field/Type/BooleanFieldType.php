<?php

namespace Leapt\AdminBundle\Datalist\Field\Type;

use Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Leapt\AdminBundle\Datalist\ViewContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BooleanFieldType
 * @package Leapt\AdminBundle\Datalist\Field\Type
 */
class BooleanFieldType extends AbstractFieldType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(array(
                'true_label' => null,
                'false_label' => null
            ))
            ->setAllowedTypes('true_label', array('null', 'string'))
            ->setAllowedTypes('false_label', array('null', 'string'))
        ;
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $row
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $row, array $options)
    {
        parent::buildViewContext($viewContext, $field, $row, $options);

        $viewContext['true_label'] = $options['true_label'];
        $viewContext['false_label'] = $options['false_label'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'boolean';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'boolean';
    }
}