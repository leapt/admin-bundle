<?php

namespace Leapt\AdminBundle\Datalist\Type;

/**
 * Class DatalistType
 * @package Leapt\AdminBundle\Datalist\Type
 */
class DatalistType extends AbstractDatalistType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'datalist';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'datalist';
    }
}