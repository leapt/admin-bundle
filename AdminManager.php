<?php

namespace Leapt\AdminBundle;

use Leapt\AdminBundle\Admin\AdminInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Environment admin service
 *
 */
class AdminManager
{
    /**
     * @var string
     */
    private $defaultTranslationDomain;
    /**
     * @var array
     */
    private $admins = array();

    /**
     * @param string $alias
     * @param \Leapt\AdminBundle\Admin\AdminInterface $admin
     */
    public function registerAdmin($alias, AdminInterface $admin, array $options)
    {
        $admin->setAlias($alias);

        $resolver = new OptionsResolver();
        $admin->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);
        $admin->setOptions($resolvedOptions);

        $this->admins[$alias] = $admin;
    }

    /**
     * @param $alias
     * @return \Leapt\AdminBundle\Admin\AdminInterface
     * @throws \InvalidArgumentException
     */
    public function getAdmin($alias)
    {
        if (!array_key_exists($alias, $this->admins)) {
            throw new \InvalidArgumentException(sprintf('The admin section %s  has not been registered with the admin bundle', $alias));
        }
        return $this->admins[$alias];
    }

    /**
     * @return array
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * @param object $entity
     * @return AdminInterface|null
     */
    public function getAdminForEntity($entity) //TODO: remove ?
    {
        $class = get_class($entity);
        foreach($this->admins as $adminCode => $admin) {
            if($admin instanceof \Leapt\AdminBundle\Admin\ContentAdmin && $class === $admin->getEntityClass()) {
                return $admin;
            }
        }
        return null;
    }

    /**
     * @param string $defaultTranslationDomain
     */
    public function setDefaultTranslationDomain($defaultTranslationDomain)
    {
        $this->defaultTranslationDomain = $defaultTranslationDomain;
    }

    /**
     * @return string
     */
    public function getDefaultTranslationDomain()
    {
        return $this->defaultTranslationDomain;
    }
}