<?php

namespace Leapt\AdminBundle\Admin;

use Leapt\CoreBundle\Datalist\DatalistFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AbstractAdmin
 * @package Leapt\AdminBundle\Admin
 */
abstract class AbstractAdmin implements AdminInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var DatalistFactory
     */
    protected $datalistFactory;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['label'])
            ->setAllowedTypes('label', 'string');
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param $name
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getOption($name)
    {
        if(!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option with name "%s" does not exist', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface;
     */
    public function getFormFactory()
    {
        return $this->container->get('form.factory');
    }

    /**
     * @param DatalistFactory $datalistFactory
     */
    public function setDatalistFactory(DatalistFactory $datalistFactory)
    {
        $this->datalistFactory = $datalistFactory;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * By default, grant access
     *
     * @param UserInterface $user
     * @param string $attribute
     * @param mixed $object
     * @return int
     */
    public function isGranted(UserInterface $user, $attribute, $object)
    {
        return VoterInterface::ACCESS_GRANTED;
    }

    /**
     * @return \Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    public function getRoutingHelper()
    {
        return $this->container->get('leapt_admin.routing_helper_content');
    }

}