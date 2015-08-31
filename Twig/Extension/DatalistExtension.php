<?php
namespace Leapt\AdminBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

use Leapt\AdminBundle\Datalist\DatalistInterface;
use Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Leapt\AdminBundle\Datalist\ViewContext;
use Leapt\AdminBundle\Datalist\Filter\DatalistFilterInterface;
use Leapt\AdminBundle\Datalist\Action\DatalistActionInterface;
use Leapt\AdminBundle\Twig\TokenParser\DatalistThemeTokenParser;


class DatalistExtension extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    /**
     * @var string
     */
    private $defaultTheme = 'LeaptAdminBundle:Datalist:datalist_grid_layout.html.twig';

    /**
     * @var \SplObjectStorage
     */
    private $themes;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->themes = new \SplObjectStorage();
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'datalist_widget' => new \Twig_Function_Method($this, 'renderDatalistWidget', array('is_safe' => array('html'))),
            'datalist_field' => new \Twig_Function_Method($this, 'renderDatalistField', array('is_safe' => array('html'))),
            'datalist_search' => new \Twig_Function_Method($this, 'renderDatalistSearch', array('is_safe' => array('html'))),
            'datalist_filters' => new \Twig_Function_Method($this, 'renderDatalistFilters', array('is_safe' => array('html'))),
            'datalist_filter' => new \Twig_Function_Method($this, 'renderDatalistFilter', array('is_safe' => array('html'))),
            'datalist_action' => new \Twig_Function_Method($this, 'renderDatalistAction', array('is_safe' => array('html')))
        );
    }

    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return array(new DatalistThemeTokenParser());
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistWidget(DatalistInterface $datalist)
    {
        $blockNames = array(
            $datalist->getType()->getBlockName(),
            '_' . $datalist->getName() . '_datalist'
        );

        $viewContext = new ViewContext();
        $datalist->getType()->buildViewContext($viewContext, $datalist, $datalist->getOptions());

        return $this->renderBlock($datalist, $blockNames, $viewContext->all());
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $row
     * @return string
     */
    public function renderDatalistField(DatalistFieldInterface $field, $row)
    {
        $blockNames = array(
            $field->getType()->getBlockName() . '_field',
            '_' . $field->getDatalist()->getName() . '_' . $field->getName() . '_field',
        );



        $viewContext = new ViewContext();
        $field->getType()->buildViewContext($viewContext, $field, $row, $field->getOptions());

        return $this->renderBlock($field->getDatalist(), $blockNames, $viewContext->all());
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistSearch(DatalistInterface $datalist)
    {
        $blockNames = array(
            'datalist_search',
            '_' . $datalist->getName() . '_search',
        );

        return $this->renderblock($datalist, $blockNames, array(
            'form' => $datalist->getSearchForm()->createView(),
            'placeholder' => $datalist->getOption('search_placeholder'),
            'submit' => $datalist->getOption('search_submit'),
            'translation_domain' => $datalist->getOption('translation_domain')
        ));
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @return string
     */
    public function renderDatalistFilters(DatalistInterface $datalist)
    {
        $blockNames = array(
            'datalist_filters',
            '_' . $datalist->getName() . '_filters'
        );

        return $this->renderblock($datalist, $blockNames, array(
            'filters' => $datalist->getFilters(),
            'datalist' => $datalist,
            'submit' => $datalist->getOption('filter_submit'),
            'reset' => $datalist->getOption('filter_reset'),
            'url' => $this->container->get('request')->getPathInfo()
        ));
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\Filter\DatalistFilterInterface $filter
     * @return string
     */
    public function renderDatalistFilter(DatalistFilterInterface $filter)
    {
        $blockNames = array(
            $filter->getType()->getBlockName() . '_filter',
            '_' . $filter->getDatalist()->getName() . '_' . $filter->getName() . '_filter'
        );
        $childForm = $filter->getDatalist()->getFilterForm()->get($filter->getName());

        return $this->renderblock($filter->getDatalist(), $blockNames, array(
            'form' => $childForm->createView(),
            'filter' => $filter,
            'datalist' => $filter->getDatalist()
        ));
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\Action\DatalistActionInterface $action
     * @param mixed $item
     * @return string
     */
    public function renderDatalistAction(DatalistActionInterface $action, $item)
    {
        $blockNames = array(
            $action->getType()->getBlockName() . '_action',
            '_' . $action->getDatalist()->getName() . '_' . $action->getName() . '_action'
        );

        $viewContext = new ViewContext();
        $action->getType()->buildViewContext($viewContext, $action, $item, $action->getOptions());

        return $this->renderblock(
            $action->getDatalist(),
            $blockNames,
            $viewContext->all()
        );
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @param array $blockNames
     * @param array $context
     * @return string
     * @throws \Exception
     */
    private function renderblock(DatalistInterface $datalist, array $blockNames, array $context = array())
    {
        $datalistTemplates = $this->getTemplatesForDatalist($datalist);
        foreach($datalistTemplates as $template) {
            if (!$template instanceof \Twig_Template) {
                $template = $this->environment->loadTemplate($template);
            }
            do {
                foreach($blockNames as $blockName) {
                    if ($template->hasBlock($blockName)) {
                        return $template->renderBlock($blockName, $context);
                    }
                }
            }
            while(($template = $template->getParent($context)) !== false);
        }

        throw new \Exception(sprintf('No block found (tried to find %s)', implode(',', $blockNames)));
    }

    /**
     * @param \Leapt\AdminBundle\Datalist\DatalistInterface $datalist
     * @return array
     */
    private function getTemplatesForDatalist(DatalistInterface $datalist)
    {
        if(isset($this->themes[$datalist])){
            return $this->themes[$datalist];
        }

        return array($this->defaultTheme);
    }

    /**
     * @param DatalistInterface $datalist
     * @param $ressources
     */
    public function setTheme(DatalistInterface $datalist, $ressources)
    {
        $this->themes[$datalist] = $ressources;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leapt_admin_datalist';
    }
}
