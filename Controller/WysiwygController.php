<?php

namespace Leapt\AdminBundle\Controller;

use Leapt\AdminBundle\Entity\File;
use Leapt\AdminBundle\Form\Type\FileType;
use Leapt\CoreBundle\Datalist\Datalist;
use Leapt\CoreBundle\Datalist\DatalistFactory;
use Leapt\CoreBundle\Datalist\Datasource\DoctrineORMDatasource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides controller to manage wysiwyg related content
 *
 */
class WysiwygController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function browserAction(Request $request)
    {
        parse_str($request->getQueryString(), $arguments);

        $file = new File();
        $uploadForm = $this->createForm(FileType::class, $file);
        $extraParameters = [];

        /** @var $datalistBuilder \Leapt\CoreBundle\Datalist\DatalistBuilder */
        $datalistBuilder = $this->get(DatalistFactory::class)
            ->createBuilder('datalist', [
                'translation_domain' => 'admin',
                'data_class'         => 'Leapt\AdminBundle\Entity\File'
            ]);

        /** @var $datalist Datalist */
        $datalist = $datalistBuilder
            ->addField('path', 'image')
            ->addField('name', 'text')
            ->addField('tags', 'text')
            ->addFilter('name', 'search', ['search_fields' => ['f.name', 'f.tags'], 'label' => 'search'])
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

                    $extraParameters = ['url' => $file->getPath()];
                }
            }
        }

        return $this->render('LeaptAdminBundle:Wysiwyg:browser.html.twig', array_merge(
            ['uploadForm' => $uploadForm->createView(), 'datalist' => $datalist, 'arguments' => $arguments],
            $extraParameters
        ));
    }
}