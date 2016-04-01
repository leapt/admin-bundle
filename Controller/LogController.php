<?php

namespace Leapt\AdminBundle\Controller;

use Leapt\CoreBundle\Paginator\Paginator;

/**
 * Class LogController
 * @package Leapt\AdminBundle\Controller
 */
class LogController extends BaseController
{
    public function listAction()
    {
        $logsQuery = $this->getDoctrine()->getRepository('LeaptAdminBundle:Log')
            ->createQueryBuilder('l')
            ->orderBy('l.createdAt','DESC')
            ->getQuery();

        $paginator = new Paginator($logsQuery, true);
        $paginator
            ->setPage($this->getRequest()->get('page'))
            ->setLimitPerPage(25);

        return $this->render('LeaptAdminBundle:Log:list.html.twig', [
            'paginator' => $paginator,
        ]);
    }
}
