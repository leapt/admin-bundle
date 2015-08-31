<?php

namespace Leapt\AdminBundle\Twig\Loader;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;

class AdminLoader extends FilesystemLoader {
    /**
     * @param string $template
     * @return string
     */
    protected function findTemplate($template)
    {
        $parts = explode(':', $template);
        $parts[1] = 'Content';
        $defaultTemplate = implode(':', $parts);

        return parent::findTemplate($defaultTemplate);
    }
}