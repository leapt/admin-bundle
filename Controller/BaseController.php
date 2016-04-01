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
     * @param string $name
     * @param string $value
     * @param array $parameters
     * @param string $domain
     */
    public function setFlash($name, $value, $parameters = [], $domain = 'LeaptAdminBundle')
    {
        $this->addFlash($name, $this->get('translator')->trans($value, $parameters, $domain));
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
    public function buildModalFlash($name, $value, $parameters = [], $domain = 'LeaptAdminBundle')
    {
        return [$name => [$this->get('translator')->trans($value, $parameters, $domain)]];
    }

    /**
     * @param string $type
     * @param int $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderError($type, $code) //TODO: check if still relevant
    {
        $translatedTitle = $this->get('translator')->trans($type . '.title', [], 'LeaptAdminBundle');
        $translatedMessages = $this->get('translator')->trans($type . '.message', [], 'LeaptAdminBundle');

        return new Response($this->renderView('LeaptAdminBundle:Error:' . $code . '.html.twig', ['title' => $translatedTitle, 'message' => $translatedMessages]), $code);
    }
}
