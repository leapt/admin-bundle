<?php

namespace Leapt\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class WidgetController
 * @package Leapt\AdminBundle\Controller
 */
class WidgetController extends BaseController
{
    public function deleteItemAction()
    {
        $responseData = array(
            'content' => $this->renderView('LeaptAdminBundle:Widget:deleteItem.html.twig')
        );

        return new JsonResponse($responseData);
    }

    /**
     * @Template
     */
    public function contentChangedAction()
    {
        return array();
    }
}