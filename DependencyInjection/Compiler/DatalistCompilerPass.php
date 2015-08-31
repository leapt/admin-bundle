<?php

namespace Leapt\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DatalistCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     *
     * @api
     */
    function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('leapt_admin.datalist_factory')) {
            return;
        }
        $definition = $container->getDefinition('leapt_admin.datalist_factory');

        // Datalist types
        foreach ($container->findTaggedServiceIds('leapt_admin.datalist_type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            $definition->addMethodCall('registerType', array($alias, new Reference($serviceId)));
        }

        // Field types
        foreach ($container->findTaggedServiceIds('leapt_admin.datalist_fieldtype') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            $definition->addMethodCall('registerFieldType', array($alias, new Reference($serviceId)));
        }

        // Filter types
        foreach ($container->findTaggedServiceIds('leapt_admin.datalist_filtertype') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            $definition->addMethodCall('registerFilterType', array($alias, new Reference($serviceId)));
        }

        // Action types
        foreach ($container->findTaggedServiceIds('leapt_admin.datalist_actiontype') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            $definition->addMethodCall('registerActionType', array($alias, new Reference($serviceId)));
        }
    }

}