<?php

namespace Leapt\AdminBundle\Datalist\Type;

use Leapt\AdminBundle\Datalist\DatalistBuilder;
use Leapt\AdminBundle\Datalist\DatalistInterface;
use Leapt\AdminBundle\Datalist\TypeInterface;
use Leapt\AdminBundle\Datalist\ViewContext;

/**
 * Interface DatalistTypeInterface
 * @package Leapt\AdminBundle\Datalist\Type
 */
interface DatalistTypeInterface extends TypeInterface
{
    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     * @return mixed
     */
    public function buildDatalist(DatalistBuilder $builder, array $options);

    /**
     * @param \Leapt\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistInterface $datalist, array $options);
}