<?php

namespace Leapt\AdminBundle\DependencyInjection\Compiler;

use Leapt\ImBundle\Manager as LeaptImManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ImCompilerPass implements CompilerPassInterface
{
    /**
     * Add admin thumb formats to LeaptImBundle if applicable
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(LeaptImManager::class)) {
            $imFormats = $container->getParameter('leapt_admin.im_formats');
            foreach ($imFormats as $name => $config) {
                $container->getDefinition(LeaptImManager::class)->addMethodCall('addFormat', [$name, $config]);
            }
        }
    }
}
