<?php

namespace Leapt\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Represent an Url field type for the multi upload form type
 *
 * Class MultiUploadUrlType
 * @package Leapt\AdminBundle\Form\Type
 */
class MultiUploadUrlType extends AbstractType
{
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_multiupload_url';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }
}
