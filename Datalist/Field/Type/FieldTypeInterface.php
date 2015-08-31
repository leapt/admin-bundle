<?php

namespace Leapt\AdminBundle\Datalist\Field\Type;

use Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Leapt\AdminBundle\Datalist\TypeInterface;
use Leapt\AdminBundle\Datalist\ViewContext;

/**
 * Interface FieldTypeInterface
 * @package Leapt\AdminBundle\Datalist\Field\Type
 */
interface FieldTypeInterface extends TypeInterface
{
    /**
     * @param \Leapt\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $value, array $options);
}