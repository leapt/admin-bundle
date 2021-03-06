<?php

namespace Leapt\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller provides generic capabilities for admin controllers
 *
 */
class BaseController extends Controller
{
    /**
     * Set a translated flash message
     *
     * @param $name
     * @param $value
     * @param array $parameters
     * @param string $domain
     * @return mixed
     */
    public function setFlash($name, $value, $parameters = array(), $domain = 'LeaptAdminBundle') //TODO: replace by getSession() usage
    {
        return $this->get('session')->getFlashBag()->add($name, $this->get('translator')->trans($value, $parameters, $domain));
    }

    /**
     * Build a translated flash message for use in modals
     *
     * @param $name
     * @param $value
     * @param array $parameters
     * @param string $domain
     * @return array
     */
    public function buildModalFlash($name, $value, $parameters = array(), $domain = 'LeaptAdminBundle')
    {
        return array($name => array($this->get('translator')->trans($value, $parameters, $domain)));
    }

    /**
     * @param $type
     * @param $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderError($type, $code) //TODO: check if still relevant
    {
        $translatedTitle = $this->get('translator')->trans($type . '.title', array(), 'LeaptAdminBundle');
        $translatedMessages = $this->get('translator')->trans($type . '.message', array(), 'LeaptAdminBundle');

        return new Response($this->renderView('LeaptAdminBundle:Error:' . $code . '.html.twig', array('title' => $translatedTitle, 'message' => $translatedMessages)), $code);
    }
}
