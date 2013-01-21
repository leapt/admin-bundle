<?php

namespace Snowcap\AdminBundle\Controller;

use Snowcap\AdminBundle\Admin\ContentAdmin;
use Snowcap\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;

use Symfony\Component\HttpFoundation\Request;

/**
 * This controller provides basic CRUD capabilities for content models
 */
class ContentController extends BaseController
{
    /**
     * Display the index screen (listing)
     *
     */
    public function indexAction(Request $request, ContentAdmin $admin)
    {
        $datalist = $admin->getDatalist();
        $datasource = new DoctrineORMDatasource($admin->getQueryBuilder());
        $datalist->setDatasource($datasource);
        $datalist->bind($request);

        $templateParams = array(
            'admin' => $admin,
            'datalist' => $datalist,
            'reorder' => false, // TODO: reimplement reorder
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig')
        );

        return $this->render(
            $this->getTemplate("SnowcapAdminBundle:Content:index.html.twig", $admin->getAlias()),
            $templateParams
        );
    }

    /**
     * Display the detail screen
     *
     */
    public function viewAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:view.html.twig', $admin->getAlias()),
            array('admin' => $admin, 'entity' => $entity)
        );
    }

    /**
     * Create a new content entity
     */
    public function createAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->buildEntity();
        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ('POST' === $request->getMethod()) {
            $forms->bind($request);
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                // TODO: reactivate using event dispatcher
                //$this->get('snowcap_admin.logger')->logContent(Logger::ACTION_CREATE, $admin, $entity, $locale);
                $this->setFlash('success', 'content.create.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl(
                        $admin,
                        'update',
                        array('id' => $entity->getId())
                    );
                } else {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl($admin, 'index');
                }

                return $this->redirect($redirectUrl);
            } else {
                $this->setFlash('error', 'content.create.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
            'form_action' => $this->getRoutingHelper()->generateUrl($admin, 'create'),
        );

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:create.html.twig', $admin->getAlias()),
            $templateParams
        );
    }

    /**
     * Update an existing content entity
     */
    public function updateAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));

        if ($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }

        $forms = $this->createForm('form');
        $form = $admin->getForm($entity);
        $forms->add($form);
        if ('POST' === $request->getMethod()) {
            $forms->bindRequest($request);
            if ($forms->isValid()) {
                $admin->saveEntity($entity);
                $admin->flush();
                // TODO: reactivate using event dispatcher
                //$this->get('snowcap_admin.logger')->logContent(Logger::ACTION_UPDATE, $admin, $entity, $locale);
                $this->setFlash('success', 'content.update.flash.success');
                $saveMode = $this->getRequest()->get('saveMode');
                if ($saveMode === ContentAdmin::SAVEMODE_CONTINUE) {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl(
                        $admin,
                        'update',
                        array('id' => $entity->getId())
                    );
                } else {
                    $redirectUrl = $this->getRoutingHelper()->generateUrl($admin, 'index');
                }

                return $this->redirect($redirectUrl);
            } else {
                $this->setFlash('error', 'content.update.flash.error');
            }
        }
        $templateParams = array(
            'admin' => $admin,
            'entity' => $entity,
            'forms' => $forms->createView(),
            'form_template' => $this->getTemplate('SnowcapAdminBundle:Content:form.html.twig', $admin->getAlias()),
            'form_theme_template' => $this->getTemplate('SnowcapAdminBundle:Form:form_layout.html.twig'),
            'form_action' => $this->getRoutingHelper()->generateUrl($admin, 'update', array('id' => $entity->getId())),
        );

        return $this->render(
            $this->getTemplate('SnowcapAdminBundle:Content:update.html.twig', $admin->getAlias()),
            $templateParams
        );
    }

    /**
     * Delete a content entity
     */
    public function deleteAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        $admin->deleteEntity($entity);
        $admin->flush();
        // TODO: reactivate using event dispatcher
        // $this->get('snowcap_admin.logger')->logContent(Logger::ACTION_DELETE, $admin, $entity, $this->getRequest()->getLocale());
        $this->setFlash('success', 'content.delete.flash.success');

        return $this->redirect($this->getRoutingHelper()->generateUrl($admin, 'index'));
    }

    /**
     * @return \Snowcap\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    private function getRoutingHelper()
    {
        return $this->get('snowcap_admin.routing_helper_content');
    }
}