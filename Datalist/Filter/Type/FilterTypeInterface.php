<?php

namespace Leapt\AdminBundle\Datalist\Filter\Type;

use Leapt\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder;
use Leapt\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Leapt\AdminBundle\Datalist\TypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Interface FilterTypeInterface
 * @package Leapt\AdminBundle\Datalist\Filter\Type
 */
interface FilterTypeInterface extends TypeInterface
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Leapt\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options);

    /**
     * @param \Leapt\AdminBundle\Datalist\Filter\DatalistFilterExpressionBuilder $builder
     * @param \Leapt\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @param mixed $value
     * @param array $options
     */
    public function buildExpression(DatalistFilterExpressionBuilder $builder, DatalistFilterInterface $filter, $value, array $options);
}