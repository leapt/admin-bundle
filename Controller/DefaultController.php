<?php
namespace Snowcap\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Snowcap\CoreBundle\Manager\PaginatorManager;

/**
 * The default admin controller is used as a dashboard for
 * admin users, and provides a few utilities methods for interface purposes
 * 
 */
class DefaultController extends BaseController
{
    /**
     * Admin default action
     *
     * @Route("", name="admin_index")
     * @Template()
     *
     * @return mixed
     */
    public function indexAction()
    {
        $logsQuery = $this->getDoctrine()->getRepository('SnowcapAdminBundle:Log')
            ->createQueryBuilder('l')
            ->orderBy('l.createdAt','DESC')
            ->getQuery();

        $logs = new PaginatorManager($logsQuery, $this->getRequest()->get('page'), 25);

        return array(
            'logs' => $logs->getResult(),
            'logsPaginator' => $logs,
        );
    }

    /**
     * Get the navigation for content management
     * 
     * @Template()
     *
     * @return mixed
     */
    public function navigationAction() {

        return array(
            'sections' => $this->get('snowcap_admin')->getAdmins(),
            'hasTranslationCatalogues' => $this->get('snowcap_admin')->hasTranslationCatalogues(),
        );
    }

    public function markdownAction() {
		$content = $this->getRequest()->request->get("content");
		$result = $this->container->get('markdown.parser')->transform($content);
		return new Response($result);
    }

    public function switchLocaleAction($locale) {
        $this->getRequest()->setLocale($locale);
        $referer = $this->getRequest()->headers->get('referer');
        return $this->redirect($referer);
    }

}
