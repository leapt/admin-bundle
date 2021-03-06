<?php

namespace Leapt\AdminBundle\Datalist\Filter\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractFilterType
 * @package Leapt\AdminBundle\Datalist\Filter\Type
 */
abstract class AbstractFilterType implements FilterTypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['property_path' => null])
            ->setDefined(['default']);
    }
}