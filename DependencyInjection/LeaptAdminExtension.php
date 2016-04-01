<?php

namespace Leapt\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;

/**
 * Extension class for the admin configuration
 * 
 */
class LeaptAdminExtension extends Extension
{
    /**
     * Load the config data for the admin bundle
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->findDefinition('leapt_admin.routing_helper_content')
            ->replaceArgument(2, $config['content']['route_prefix'])
            ->replaceArgument(3, $config['content']['route_name_prefix']);

        $container->findDefinition('leapt_admin')->addMethodCall(
            'setDefaultTranslationDomain',
            [$config['default_translation_domain']]
        );

        foreach (['user_class'] as $option) {
            if (isset($config['security'][$option])) {
                $container->setParameter('leapt_admin.security.' . $option, $config['security'][$option]);
            }
        }

        $container->setParameter('leapt_admin.im_formats', $config['im_formats']);

        if (array_key_exists('translation_catalogues', $config)) {
            $container->setParameter('leapt_admin.translation_catalogues', $config['translation_catalogues']);
        }

        $container->setParameter('leapt_admin.multiupload.tmp_dir', $config['multiupload']['tmp_dir']);

        if (null !== $config['default_locale'] && isset($_SERVER['REQUEST_URI']) && 0 === strpos($_SERVER['REQUEST_URI'], $config['route_prefix'])) {
            $container->setParameter('kernel.default_locale', $config['default_locale']);
        }
    }
}
