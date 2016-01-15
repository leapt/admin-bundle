<?php

namespace Leapt\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class MarkdownType
 * @package Leapt\AdminBundle\Form\Type
 */
class MarkdownType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_markdown';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextareaType::class;
    }
}