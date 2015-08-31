<?php

namespace Leapt\AdminBundle\Datalist\Datasource;

use Leapt\AdminBundle\Datalist\Filter\Expression\ExpressionInterface;

/**
 * Interface DatasourceInterface
 * @package Leapt\AdminBundle\Datalist\Datasource
 */
interface DatasourceInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param int $limitPerPage
     * @param int $rangeLimit
     *
     * @return DatasourceInterface
     */
    public function paginate($limitPerPage, $rangeLimit);

    /**
     * @param int $page
     */
    public function setPage($page);

    /**
     * @param \Leapt\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return mixed
     */
    public function setSearchExpression(ExpressionInterface $expression);

    /**
     * @param \Leapt\AdminBundle\Datalist\Filter\Expression\ExpressionInterface $expression
     * @return mixed
     */
    public function setFilterExpression(ExpressionInterface $expression);

    /**
     * @return \Leapt\CoreBundle\Paginator\PaginatorInterface
     */
    public function getPaginator();

    /**
     * @param string $field
     * @param string $direction
     * @throws \InvalidArgumentException
     */
    public function setSort($field, $direction);
}