<?php

namespace Leapt\AdminBundle\Controller;

use Leapt\AdminBundle\Datalist\Datalist;
use Leapt\AdminBundle\Datalist\Datasource\DoctrineORMDatasource;
use Leapt\AdminBundle\Entity\File;
use Leapt\AdminBundle\Form\Type\FileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides controller to manage wysiwyg related content
 *
 */
class WysiwygController extends BaseController
{
    /**
     * @return array
     *
     * @Template()
     */
    public function browserAction()
    {
        /** @var $request Request */
        $request = $this->get('request');

        parse_str($this->getRequest()->getQueryString(), $arguments);

        $file = new File();
        $uploadForm = $this->createForm(new FileType(), $file);
        $extraParameters = array();

        /** @var $datalistBuilder \Leapt\AdminBundle\Datalist\DatalistBuilder */
        $datalistBuilder = $this
            ->get('leapt_admin.datalist_factory')
            ->createBuilder(
                'datalist',
                array(
                    'translation_domain' => 'admin',
                    'data_class' => 'Leapt\AdminBundle\Entity\File'
                )
            );
        /** @var $datalist Datalist */
        $datalist = $datalistBuilder
            ->addField('path', 'image')
            ->addField('name', 'text')
            ->addField('tags', 'text')
            ->addFilter('name', 'search', array('search_fields' => array('f.name', 'f.tags'), 'label' => 'search'))
            ->getDatalist();

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('f')->from('LeaptAdminBundle:File', 'f');

        $datasource = new DoctrineORMDatasource($queryBuilder);
        $datalist->setDatasource($datasource);
        $datalist->bind($request);

        if ('POST' === $request->getMethod()) {
            // Manage upload post
            if ($request->get('admin_leapt_file') !== null) {
                $uploadForm->handleRequest($request);
                if ($uploadForm->isValid()) {
                    $em->persist($file);
                    $em->flush();

                    $extraParameters = array('url' => $file->getPath());
                }
            }
        }

        return array_merge(array('uploadForm' => $uploadForm->createView(), 'datalist' => $datalist, 'arguments' => $arguments), $extraParameters);
    }
}