<?php

namespace Leapt\AdminBundle\Datalist\Filter;

interface DatalistFilterInterface
{
    /**
     * @return \Leapt\AdminBundle\Datalist\Filter\Type\FilterTypeInterface
     */
    public function getType();

    /**
     * @return \Leapt\AdminBundle\Datalist\DatalistInterface
     */
    public function getDatalist();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPropertyPath();

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
}