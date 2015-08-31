<?php

namespace Leapt\AdminBundle\Routing\Helper;

use Leapt\AdminBundle\Admin\AdminInterface;
use Leapt\CoreBundle\Util\StringUtil;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ContentRoutingHelper
 * @package Leapt\AdminBundle\Routing\Helper
 */
class ContentRoutingHelper
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser
     */
    private $parser;

    /**
     * @var string
     */
    private $routeNamePrefix;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser $parser
     * @param string $routePrefix
     * @param string $routeNamePrefix
     */
    public function __construct(RouterInterface $router, ControllerNameParser $parser, $routePrefix, $routeNamePrefix)
    {
        $this->router = $router;
        $this->parser = $parser;
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = $routeNamePrefix;
    }

    public function getRouteName(AdminInterface $admin, $action)
    {
        return $this->routeNamePrefix . '_' . $admin->getAlias() . '_' . $action;
    }

    /**
     * Build a route for the given admin and action, with the provided params
     *
     * This method will first attempt to find a custom Route (like "YourCustomAdminBundle:Section:index")
     * and if it does not work
     *
     * @param \Leapt\AdminBundle\Admin\AdminInterface $admin
     * @param string $action
     * @param array $params
     * @param bool $defaultRoute
     * @return \Symfony\Component\Routing\Route
     */
    public function getRoute(AdminInterface $admin, $action, $params = array(), $defaultRoute = false)
    {
        $defaults = array();
        $pattern = '/' . $admin->getAlias();
        if(!$defaultRoute) {
            $pattern .= '/' . $action;
        }
        foreach($params as $paramKey => $paramValue) {
            if(is_int($paramKey)) {
               $paramName = $paramValue;
            }
            else {
                $paramName = $paramKey;
                $defaults[$paramName] = $paramValue;
            }

            $pattern .= '/{' . $paramName . '}';
        }

        preg_match('/(?:[A-Z](?:[A-Za-z0-9])+\\\)*(?:)[A-Z](?:[A-Za-z0-9])+Bundle/', get_class($admin), $matches);
        $bundle = implode('', explode('\\', $matches[0]));
        $section = StringUtil::camelize($admin->getAlias());
        $controller = $bundle . ':' . $section . ':' . $action;
        try {
            $controller = $this->parser->parse($controller);
        }
        catch(\InvalidArgumentException $e) {
            $controller = $this->parser->parse('LeaptAdminBundle:Content:' . $action);
        }

        $defaults = array_merge(
            array('_controller' => $controller, 'alias' => $admin->getAlias()),
            $defaults
        );
        return new Route($pattern, $defaults);
    }

    /**
     * @param \Leapt\AdminBundle\Admin\AdminInterface $admin
     * @param string $action
     * @param array $params
     * @return string
     */
    public function generateUrl(AdminInterface $admin, $action, $params = array())
    {
        $routeName = $this->getRouteName($admin, $action);

        return $this->router->generate($routeName, $params);
    }
}