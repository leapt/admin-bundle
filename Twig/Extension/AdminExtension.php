<?php

namespace Leapt\AdminBundle\Twig\Extension;

use Leapt\AdminBundle\AdminManager;
use Leapt\AdminBundle\Admin\AdminInterface;
use Leapt\AdminBundle\Admin\ContentAdmin;
use Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Global, general-purpose admin extension
 */
class AdminExtension extends \Twig_Extension
{
    /**
     * @var \Leapt\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @var \Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    private $contentRoutingHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param \Leapt\AdminBundle\AdminManager $adminManager
     * @param \Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper $contentRoutingHelper
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(
        AdminManager $adminManager,
        ContentRoutingHelper $contentRoutingHelper,
        TranslatorInterface $translator
    ) {
        $this->adminManager = $adminManager;
        $this->contentRoutingHelper = $contentRoutingHelper;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_admin_for_entity_name', [$this, 'getAdminForEntityName']),
            new \Twig_SimpleFunction('admin', [$this, 'getAdminByCode']),
            new \Twig_SimpleFunction('admin_label', [$this, 'getAdminLabel']),
            new \Twig_SimpleFunction('admin_content_path', [$this, 'getAdminContentPath']),
            new \Twig_SimpleFunction('admin_translation_domain', [$this, 'getDefaultTranslationDomain']),
        ];
    }

    /**
     * @param $code
     * @return \Leapt\AdminBundle\Admin\AdminInterface
     */
    public function getAdminByCode($code)
    {
        return $this->adminManager->getAdmin($code);
    }

    /**
     * @param $namespace
     * @return \Leapt\AdminBundle\Admin\ContentAdmin
     */
    public function getAdminForEntityName($namespace)
    {
        $entity = new $namespace;
        $admin = $this->adminManager->getAdminForEntity($entity);

        return $admin;
    }

    /**
     * @param \Leapt\AdminBundle\Admin\ContentAdmin $admin
     * @param string $action
     * @param array $params
     * @return string
     */
    public function getAdminContentPath($admin, $action, array $params = array())
    {
        if(!$admin instanceof ContentAdmin) {
            $admin = $this->getAdminByCode($admin);
        }

        return $this->contentRoutingHelper->generateUrl($admin, $action, $params);
    }

    /**
     * @param \Leapt\AdminBundle\Admin\AdminInterface $admin
     * @param bool $plural
     * @return string
     */
    public function getAdminLabel(AdminInterface $admin, $plural = false)
    {
        $number = $plural ? 10 : 1;
        $label = $admin->getOption('label');

        return $this->translator->transChoice($label, $number, array(), $this->adminManager->getDefaultTranslationDomain());
    }

    /**
     * @return string
     */
    public function getDefaultTranslationDomain()
    {
        return $this->adminManager->getDefaultTranslationDomain();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leapt_admin';
    }
}
