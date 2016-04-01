<?php

namespace Leapt\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The default admin controller is used as a dashboard for
 * admin users, and provides a few utilities methods for interface purposes
 */
class DefaultController extends BaseController
{
    /**
     * Admin default action
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('LeaptAdminBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function markdownAction(Request $request) //TODO: move elsewhere
    {
		$result = $this->container->get('markdown.parser')->transform($request->request->get('content'));

        return new Response($result);
    }
}
