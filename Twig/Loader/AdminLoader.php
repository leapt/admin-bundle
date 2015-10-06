<?php

namespace Leapt\AdminBundle\Twig\Loader;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;

/**
 * Class AdminLoader
 * @package Leapt\AdminBundle\Twig\Loader
 */
class AdminLoader extends FilesystemLoader
{
    /**
     * @param string $template
     * @param bool $throw
     * @return string
     */
    protected function findTemplate($template, $throw = true)
    {
        $parts = explode(':', $template);
        $parts[1] = 'Content';
        $defaultTemplate = implode(':', $parts);

        return parent::findTemplate($defaultTemplate, $throw);
    }
}