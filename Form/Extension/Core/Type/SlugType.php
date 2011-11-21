<?php
namespace Snowcap\AdminBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Slug field type class
 * 
 */
class SlugType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'slug';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options) {
        return array('target' => 'title');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->setAttribute('target', $options['target']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        parent::buildView($view, $form);
        $target = $form->getParent()->getName() . '_' . $form->getAttribute('target');
        $view->set('target', $target);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'text';
    }
}