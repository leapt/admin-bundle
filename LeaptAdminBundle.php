<?php

namespace Leapt\AdminBundle;

use Leapt\AdminBundle\DependencyInjection\Compiler\AdminCompilerPass;
use Leapt\AdminBundle\DependencyInjection\Compiler\DatalistCompilerPass;
use Leapt\AdminBundle\DependencyInjection\Compiler\ImCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LeaptAdminBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AdminCompilerPass());
        $container->addCompilerPass(new DatalistCompilerPass());
        $container->addCompilerPass(new ImCompilerPass());
    }
}
