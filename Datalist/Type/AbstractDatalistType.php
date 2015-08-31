<?php

namespace Leapt\AdminBundle\Datalist\Type;

use Leapt\AdminBundle\Datalist\DatalistBuilder;
use Leapt\AdminBundle\Datalist\DatalistInterface;
use Leapt\AdminBundle\Datalist\ViewContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractDatalistType
 * @package Leapt\AdminBundle\Datalist\Type
 */
abstract class AbstractDatalistType implements DatalistTypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null,
                'layout' => 'grid',
                'filter_on_top' => false, //TODO: should be removed if confirmed that not used
                'filters_on_top' => false,
                'limit_per_page' => null,
                'range_limit' => 10,
                'search_placeholder' => null,
                'search_submit' => 'datalist.search.submit',
                'filter_submit' => 'datalist.filter.submit',
                'filter_reset' => 'datalist.filter.reset',
                'translation_domain' => 'messages'
            ))
            ->setDefined(array(
                'search'
            ));
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistBuilder $builder
     * @param array $options
     * @return mixed|void
     */
    public function buildDatalist(DatalistBuilder $builder, array $options)
    {

    }

    /**
     * @param \Leapt\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistInterface $datalist, array $options)
    {
        $viewContext['datalist'] = $datalist;
        $viewContext['options'] = $options;
        $viewContext['translation_domain'] = $options['translation_domain'];
    }
}