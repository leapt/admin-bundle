<?php

namespace Leapt\AdminBundle\Datalist\Field;

use Leapt\AdminBundle\Datalist\DatalistInterface;

/**
 * Interface DatalistFieldInterface
 * @package Leapt\AdminBundle\Datalist\Field
 */
interface DatalistFieldInterface
{
    /**
     * @return \Leapt\AdminBundle\Datalist\Field\Type\FieldTypeInterface
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
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value);

    /**
     * @param mixed $row
     * @return mixed
     */
    public function getData($row);

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     */
    public function setDatalist(DatalistInterface $datalist);

    /**
     * @return DatalistInterface
     */
    public function getDatalist();
}