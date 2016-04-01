<?php

namespace Leapt\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class WidgetController
 * @package Leapt\AdminBundle\Controller
 */
class WidgetController extends BaseController
{
    public function deleteItemAction()
    {
        $responseData = [
            'content' => $this->renderView('LeaptAdminBundle:Widget:deleteItem.html.twig')
        ];

        return new JsonResponse($responseData);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contentChangedAction()
    {
        return $this->render('LeaptAdminBundle:Widget:contentChanged.html.twig');
    }
}