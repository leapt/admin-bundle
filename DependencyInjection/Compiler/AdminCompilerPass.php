<?php

namespace Leapt\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('leapt_admin')) {
            return;
        }

        $definition = $container->getDefinition('leapt_admin');
        foreach ($container->findTaggedServiceIds('leapt_admin.admin') as $serviceId => $tag) {
            $adminTag = $tag[0];

            $alias = isset($adminTag['alias'])
                ? $adminTag['alias']
                : $serviceId;

            if(!isset($adminTag['label'])) {
                $adminTag['label'] = $serviceId;
            }

            unset($adminTag['alias']);

            $definition->addMethodCall('registerAdmin', array($alias, new Reference($serviceId), $adminTag));
        }
    }
}