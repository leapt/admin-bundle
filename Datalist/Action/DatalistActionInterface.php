<?php

namespace Leapt\AdminBundle\Datalist\Action;

use Leapt\AdminBundle\Datalist\DatalistInterface;

/**
 * Interface DatalistActionInterface
 * @package Leapt\AdminBundle\Datalist\Action
 */
interface DatalistActionInterface
{
    /**
     * @return \Leapt\AdminBundle\Datalist\Action\Type\ActionTypeInterface
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name);

    /**
     * @param string $name
     * @param mixed $default
     */
    public function getOption($name, $default = null);

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     */
    public function setDatalist(DatalistInterface $datalist);

    /**
     * @return DatalistInterface
     */
    public function getDatalist();
}