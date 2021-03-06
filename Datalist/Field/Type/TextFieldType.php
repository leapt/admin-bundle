<?php

namespace Leapt\AdminBundle\Datalist\Field\Type;

use Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Leapt\AdminBundle\Datalist\ViewContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TextFieldType
 * @package Leapt\AdminBundle\Datalist\Field\Type
 */
class TextFieldType extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'text';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefined(array('truncate'));
    }

    /**
     * @param ViewContext $viewContext
     * @param DatalistFieldInterface $field
     * @param mixed $row
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $row, array $options)
    {
        parent::buildViewContext($viewContext, $field, $row, $options);

        if (isset($options['truncate'])) {
            $viewContext['truncate'] = $options['truncate'];
        }
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'text';
    }
}