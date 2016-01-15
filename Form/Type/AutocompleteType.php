<?php

namespace Leapt\AdminBundle\Form\Type;

use Leapt\AdminBundle\AdminManager;
use Leapt\AdminBundle\Admin\ContentAdmin;
use Leapt\AdminBundle\Form\DataTransformer\EntityToIdTransformer;
use Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class AutocompleteType
 * @package Leapt\AdminBundle\Form\Type
 */
class AutocompleteType extends AbstractType
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
     * @param \Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper $routingHelper
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
        $adminManager = $this->adminManager;

        $resolver
            ->setDefaults([
                'allow_add' => false,
                'add_label' => 'Add new',
                'multiple' => false,
                'compound' => function (Options $options) {
                    return $options['multiple'];
                },
                'id_property' => 'id',
                'property' => '__toString',
            ])
            ->setRequired(['admin', 'where'])
            ->setDefined(['row_id_property', 'row_property'])
            ->setNormalizer('admin', function (Options $options, $adminOption) use ($adminManager) {
                if (!$adminOption instanceof ContentAdmin) {
                    return $adminManager->getAdmin($adminOption);
                }

                return $adminOption;
            });
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EntityToIdTransformer($options['admin'], $options['multiple']));

        if ($options['multiple']) {
            $prototype = $builder->create('__name__', HiddenType::class);
            $builder->setAttribute('prototype', $prototype->getForm());

            $resizeListener = new ResizeFormListener(
                HiddenType::class,
                [],
                true,
                true
            );

            $builder->addEventSubscriber($resizeListener);
        }
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $form->getData();
        // For multiple autocomplete, we need to store textual values indexed by value, along with the prototype
        if($options['multiple']) {
            // Fix: If $value is null we must cast it as an array
            if (null === $value) {
                $value = [];
            }

            $textValues = [];
            foreach ($value as $entity) {
                $textValues[$entity->getId()]= $this->buildTextValue($entity, $options);
            }
            $view->vars['text_values'] = $textValues;
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }
        // For single autocomplete, just store the only textual value
        else {
            $textValue = $this->buildTextValue($value, $options);
            $view->vars['text_value'] = $textValue;
        }

        // Set the other variables
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['list_url'] = $this->generateListUrl($options);
        $view->vars['allow_add'] = $options['allow_add'];
        if($options['allow_add']) {
            $view->vars['add_url'] = $this->generateAddUrl($options);
            $view->vars['add_label'] = $options['add_label'];
        }
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return FormType::class;
    }

    /**
     * @param mixed $value
     * @param array $options
     * @return string
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    private function buildTextValue($value, array $options)
    {
        if (null === $value) {
            $textValue = '';
        } else {
            try {
                $accessor = PropertyAccess::createPropertyAccessor();
                $textValue = $accessor->getValue($value, $options['property']);
            }
            catch (NoSuchPropertyException $e) {
                if ('__toString' === $options['property']) {
                    $message = 'You must provide a "property" option (or your class must implement the "__toString" method';
                } else {
                    $message = sprintf('The "%s" is not a valid property option', $options['property']);
                }
                throw new MissingOptionsException($message);
            }
        }

        return $textValue;
    }

    /**
     * @param array $options
     * @return string
     */
    private function generateListUrl(array $options)
    {
        $rowIdProperty = isset($options['row_id_property']) ?
            $options['row_id_property'] :
            $options['id_property'];

        $rowProperty = isset($options['row_property']) ?
            $options['row_property'] :
            $options['property'];

        return $this->routingHelper->generateUrl(
            $options['admin'],
            'autocompleteList',
            [
                'query' => '__query__',
                'where' => base64_encode($options['where']),
                'id_property' => $rowIdProperty,
                'property' => $rowProperty,
            ]
        );
    }

    /**
     * @param array $options
     * @return string
     */
    private function generateAddUrl(array $options)
    {
        return $this->routingHelper->generateUrl($options['admin'], 'modalCreate');
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'leapt_admin_autocomplete';
    }
}
