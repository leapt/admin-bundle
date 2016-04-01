<?php

namespace Leapt\AdminBundle\Form\Type;

use Leapt\AdminBundle\AdminManager;
use Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EntityType
 * @package Leapt\AdminBundle\Form\Type
 */
class EntityType extends AbstractType
{
    /**
     * @var \Leapt\AdminBundle\AdminManager
     */
    private $adminManager;

    /**
     * @var ContentRoutingHelper
     */
    private $routingHelper;

    /**
     * @param \Leapt\AdminBundle\AdminManager $adminManager
     * @param ContentRoutingHelper $routingHelper
     */
    public function __construct(AdminManager $adminManager, ContentRoutingHelper $routingHelper)
    {
        $this->adminManager = $adminManager;
        $this->routingHelper = $routingHelper;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_add' => false,
                'add_label' => 'Add new'
            ])
            ->setRequired(['admin']);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_add'] = $options['allow_add'];
        if($options['allow_add']) {
            $view->vars['add_url'] = $this->routingHelper->generateUrl($this->adminManager->getAdmin($options['admin']), 'modalCreate');
            $view->vars['add_label'] = $options['add_label'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leapt_admin_entity';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'entity';
    }
}
