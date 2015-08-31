<?php

namespace Leapt\AdminBundle\Datalist\Action\Type;

use Leapt\AdminBundle\Datalist\Action\DatalistActionInterface;
use Leapt\AdminBundle\Datalist\TypeInterface;
use Leapt\AdminBundle\Datalist\ViewContext;

/**
 * Interface ActionTypeInterface
 * @package Leapt\AdminBundle\Datalist\Action\Type
 */
interface ActionTypeInterface extends TypeInterface
{
    /**
     * @param \Leapt\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param $item
     * @param array $options
     * @return string
     */
    public function getUrl(DatalistActionInterface $action, $item, array $options = array());

    /**
     * @param \Leapt\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Leapt\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param $item
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistActionInterface $action, $item, array $options);
}