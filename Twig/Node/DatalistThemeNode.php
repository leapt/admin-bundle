<?php

namespace Leapt\AdminBundle\Twig\Node;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DatalistThemeNode extends \Twig_Node
{
    /**
     * @param \Twig_NodeInterface $datalist
     * @param \Twig_NodeInterface $resources
     * @param $lineno
     * @param null $tag
     */
    public function __construct(\Twig_NodeInterface $datalist, \Twig_NodeInterface $resources, $lineno, $tag = null)
    {
        parent::__construct(['datalist' => $datalist, 'resources' => $resources], [], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$this->env->getExtension(\'Leapt\AdminBundle\Twig\Extension\DatalistExtension\')->setTheme(')
            ->subcompile($this->getNode('datalist'))
            ->raw(', ')
            ->subcompile($this->getNode('resources'))
            ->raw(");\n");
        ;
    }
}