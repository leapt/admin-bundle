<?php

namespace Leapt\AdminBundle\Datalist\Field\Type;

/**
 * Class HeadingFieldType
 * @package Leapt\AdminBundle\Datalist\Field\Type
 */
class HeadingFieldType extends TextFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'heading';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'heading';
    }
}