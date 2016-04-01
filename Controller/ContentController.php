<?php

namespace Leapt\AdminBundle\Controller;

use Leapt\AdminBundle\Admin\AdminInterface;
use Leapt\AdminBundle\Admin\ContentAdmin;
use Leapt\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;
use Leapt\AdminBundle\Exception\ValidationException;
use Leapt\CoreBundle\Util\StringUtil;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This controller provides basic CRUD capabilities for content models
 *
 */
class ContentController extends BaseController
{
    /**
     * Display the index screen (listing)
     *
     * @param Request $request
     * @param ContentAdmin $admin
     * @return Response
     */
    public function indexAction(Request $request, ContentAdmin $admin)
    {
        $this->secure($admin, 'ADMIN_CONTENT_LIST');

        $datalist = $admin->getDatalist();
        $datalist->setRoute($request->attributes->get('_route'))
            ->setRouteParams($request->query->all());
        $datasource = new DoctrineORMDatasource($admin->getQueryBuilder());
        $datalist->setDatasource($datasource);
        $datalist->bind($request);

        return $this->render('LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':index.html.twig', [
            'admin'     => $admin,
            'datalist'  => $datalist
        ]);
    }

    /**
     * Display the detail screen
     *
     * @param Request $request
     * @param ContentAdmin $admin
     * @return Response
     */
    public function viewAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        $this->secure($admin, 'ADMIN_CONTENT_VIEW', $entity);

        return $this->render('LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':view.html.twig', [
            'admin'  => $admin,
            'entity' => $entity
        ]);
    }

    /**
     * Create a new content entity
     *
     */
    public function createAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->buildEntity();
        $this->secure($admin, 'ADMIN_CONTENT_CREATE', $entity);

        $form = $admin->getForm();
        $form->setData($entity);

        if ($request->isMethod('POST')) {
            try {
                $this->save($request, $admin, $form, $entity);
                $this->buildEntityFlash('success', 'content.create.flash.success', $admin, $entity);
                $redirectUrl = $request->get('saveMode') === ContentAdmin::SAVEMODE_CONTINUE ?
                    $this->getRoutingHelper()->generateUrl($admin, 'update', ['id' => $entity->getId()]) :
                    $this->getRoutingHelper()->generateUrl($admin, 'index');

                return $this->redirect($redirectUrl);
            }
            catch(ValidationException $e) {
                $this->buildEntityFlash('error', 'content.create.flash.error', $admin, $entity);
                $this->get('logger')->addError($e->getMessage());
            }
        }

        return $this->render('LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':create.html.twig', [
            'admin' => $admin,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Create a new content entity through ajax modal
     *
     */
    public function modalCreateAction(Request $request, ContentAdmin $admin) {
        $entity = $admin->buildEntity();
        $this->secure($admin, 'ADMIN_CONTENT_CREATE', $entity);

        $form = $admin->getForm();
        $form->setData($entity);

        $status = 200;

        if ('POST' === $request->getMethod()) {
            try {
                $this->save($request, $admin, $form, $entity);
                $result = [
                    'entity_id'   => $entity->getId(),
                    'entity_name' => $admin->getEntityName($entity)
                ];

                return new JsonResponse(['result' => $result], 201);
            }
            catch(ValidationException $e) {
                $status = 400;
                $this->buildEntityFlash('error', 'content.create.flash.error', $admin, $entity);
                $this->get('logger')->addError($e->getMessage());
            }
        }

        $responseData = [
            'content' => $this->renderView('LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':modalCreate.html.twig', [
                'admin'  => $admin,
                'entity' => $entity,
                'form'   => $form->createView(),
            ])
        ];

        return new JsonResponse($responseData, $status);
    }

    /**
     * Update an existing content entity
     *
     */
    public function updateAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        if ($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }
        $this->secure($admin, 'ADMIN_CONTENT_UPDATE', $entity);

        $form = $admin->getForm();
        $form->setData($entity);

        if ($request->isMethod('POST')) {
            try {
                $this->save($request, $admin, $form, $entity);
                $this->buildEntityFlash('success', 'content.update.flash.success', $admin, $entity);
                $redirectUrl = $request->get('saveMode') === ContentAdmin::SAVEMODE_CONTINUE ?
                    $this->getRoutingHelper()->generateUrl($admin, 'update', ['id' => $entity->getId()]) :
                    $this->getRoutingHelper()->generateUrl($admin, 'index');

                return $this->redirect($redirectUrl);
            }
            catch(ValidationException $e) {
                $this->buildEntityFlash('error', 'content.update.flash.error', $admin, $entity);
                $this->get('logger')->addError($e->getMessage());
            }
        }

        return $this->render('LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':update.html.twig', [
            'admin'  => $admin,
            'entity' => $entity,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * Update an existing content entity
     *
     */
    public function modalUpdateAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        if ($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }
        $this->secure($admin, 'ADMIN_CONTENT_UPDATE', $entity);

        $form = $admin->getForm();
        $form->setData($entity);

        $status = 200;

        if ('POST' === $request->getMethod()) {
            try {
                $this->save($request, $admin, $form, $entity);

                $result = [
                    'entity_id'   => $entity->getId(),
                    'entity_name' => $admin->getEntityName($entity),
                ];

                return new JsonResponse([
                    'result'  => $result,
                    'flashes' => $this->buildEntityFlash('success', 'content.update.flash.success', $admin, $entity)
                ], 201);
            } catch (ValidationException $e) {
                $status = 400;
                $this->buildEntityFlash('error', 'content.update.flash.error', $admin, $entity);
                $this->get('logger')->addError($e->getMessage());
            }
        }

        $responseData = [
            'content' => $this->renderView('LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':modalUpdate.html.twig', [
                'admin'  => $admin,
                'entity' => $entity,
                'form'   => $form->createView(),
            ])
        ];

        return new JsonResponse($responseData, $status);
    }

    /**
     * Delete a content entity
     *
     */
    public function deleteAction(Request $request, ContentAdmin $admin)
    {
        $entity = $admin->findEntity($request->attributes->get('id'));
        $this->secure($admin, 'ADMIN_CONTENT_DELETE', $entity);

        if($request->isXmlHttpRequest()) {
            return $this->modalDelete($request, $admin, $entity);
        }
        else {
            return $this->delete($request, $admin, $entity);
        }
    }

    /**
     * Handle AJAX delete (modal)
     *
     * @param Request $request
     * @param ContentAdmin $admin
     * @param $entity
     * @return JsonResponse
     */
    public function modalDelete(Request $request, ContentAdmin $admin, $entity)
    {
        $status = 200;

        if (null === $entity) {
            $content = $this->renderView(
                'LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':modalError.html.twig'
            );
        } else {
            if($request->isMethod('post')) {
                try {
                    $admin->deleteEntity($entity);
                    $this->buildEntityFlash('success', 'content.delete.flash.success', $admin, $entity);
                    $result = [
                        'entity_id'   => $entity->getId(),
                        'entity_name' => $admin->getEntityName($entity)
                    ];
                    $redirectUrl = $request->headers->get('referer');

                    return new JsonResponse(['result' => $result, 'redirect_url' => $redirectUrl], 301);

                } catch (\Exception $e) {
                    $status = 400;
                    $this->buildEntityFlash('error', 'content.delete.flash.error', $admin, $entity);
                    $this->get('logger')->addError($e->getMessage());
                }
            }

            $content = $this->renderView(
                'LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':modalDelete.html.twig',
                [
                    'admin'  => $admin,
                    'entity' => $entity,
                ]
            );
        }

        return new JsonResponse(['content' => $content], $status);
    }

    /**
     * Handle standard delete (no modal)
     *
     * @param Request $request
     * @param ContentAdmin $admin
     * @param $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function delete(Request $request, ContentAdmin $admin, $entity)
    {
        if ($entity === null) {
            return $this->renderError('error.content.notfound', 404);
        }

        if($request->isMethod('post')) {
            try {
                $admin->deleteEntity($entity);
                $this->buildEntityFlash('success', 'content.delete.flash.success', $admin, $entity);

            } catch(\Exception $e) {
                $this->buildEntityFlash('error', 'content.delete.flash.error', $admin, $entity);
                $this->get('logger')->addError($e->getMessage());
            }

            return $this->redirect($this->getRoutingHelper()->generateUrl($admin, 'index'));
        }

        return $this->render(
            'LeaptAdminBundle:' . StringUtil::camelize($admin->getAlias()) . ':modalDelete.html.twig',
            [
                'admin' => $admin,
                'entity' => $entity,
            ]
        );
    }

    /**
     * Render a json array of entity values and text (to be used in autocomplete widgets)
     *
     */
    public function autocompleteListAction(ContentAdmin $admin, $where, $id_property, $property, $query) {
        $qb = $admin->getQueryBuilder();
        $results = $qb
            ->andWhere(base64_decode($where))
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        $flattenedResults = [];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($results as $result) {
            $id = $accessor->getValue($result, $id_property);
            $value = $accessor->getValue($result, $property);
            $flattenedResults[] = ['id' => $id, 'value' => $value];
        }

        return new JsonResponse($flattenedResults);
    }

    /**
     * Save a content entity
     *
     * @param \Symfony\Component\HttpFoundation\Request
     * @param \Leapt\AdminBundle\Admin\ContentAdmin $admin
     * @param \Symfony\Component\Form\Form $form
     * @param object $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws ValidationException
     */
    protected function save(Request $request, ContentAdmin $admin, Form $form, $entity)
    {
        $form->handleRequest($request);
        if ($form->isValid()) {
            $admin->saveEntity($entity);
        } else {
            throw new ValidationException('could not save');
        }
    }

    /**
     * @param AdminInterface $admin
     * @param mixed $attributes
     * @param object $object
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function secure(AdminInterface $admin, $attributes, $object = null)
    {
        if (!is_array($attributes)) {
            $attributes = [$attributes];
        }
        $suffixedAttributes = array_map(function ($attribute) use ($admin) {
            return $attribute . '__' . strtoupper($admin->getAlias());
        }, $attributes);
        if (!$this->getAuthorizationChecker()->isGranted($suffixedAttributes, $object)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Generate a flash message for the provided admin and entity
     *
     * @param string $type
     * @param string $message
     * @param ContentAdmin $admin
     * @param object $entity
     * @param string $domain
     */
    protected function buildEntityFlash($type, $message, ContentAdmin $admin, $entity, $domain = 'LeaptAdminBundle')
    {
        $this->addFlash($type, $this->get('translator')->trans(
            $message,
            [
                '%type%' => $this->get('translator')->transChoice(
                    $admin->getOption('label'), 1, [], $this->get('leapt_admin')->getDefaultTranslationDomain()
                ),
                '%name%' => $admin->getEntityName($entity)
            ],
            $domain
        ));
    }

    /**
     * @return \Leapt\AdminBundle\Routing\Helper\ContentRoutingHelper
     */
    protected function getRoutingHelper()
    {
        return $this->get('leapt_admin.routing_helper_content');
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        return $this->get('security.authorization_checker');
    }
}