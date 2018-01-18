<?php

namespace Leapt\AdminBundle\Routing\Loader;

use Leapt\AdminBundle\AdminManager;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class AdminLoader extends Loader
{
    /**
     * @var \Leapt\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @param \Leapt\AdminBundle\AdminManager $adminManager
     */
    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'leapt_admin_extra' === $type;
    }

    /**
     * Loads a resource.
     *
     * @param mixed  $resource The resource
     * @param string $type     The resource type
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        foreach ($this->adminManager->getAdmins() as $alias => $admin) {
            $admin->addRoutes($routes);
        }

        return $routes;
    }
}