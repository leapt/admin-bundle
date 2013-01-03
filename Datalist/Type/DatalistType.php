<?php

namespace Snowcap\AdminBundle\Datalist\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\DatalistBuilder;

class DatalistType extends AbstractDatalistType {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'display_mode' => 'grid'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datalist';
    }
}