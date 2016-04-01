<?php

namespace Leapt\AdminBundle\Controller;

/**
 * The default admin controller is used as a dashboard for
 * admin users, and provides a few utilities methods for interface purposes
 * 
 */
class NavigationController extends BaseController
{
    /**
     * Get the navigation for content management
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainAction()
    {
        return $this->render('LeaptAdminBundle:Navigation:main.html.twig', [
            'admins' => $this->get('leapt_admin')->getAdmins(),
        ]);
    }
}
