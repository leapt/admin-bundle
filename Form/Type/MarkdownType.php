<?php

namespace Leapt\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class MarkdownType
 * @package Leapt\AdminBundle\Form\Type
 */
class MarkdownType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'leapt_admin_markdown';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'textarea';
    }
}