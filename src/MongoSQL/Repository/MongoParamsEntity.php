<?php

namespace MongoSQL\Repository;

class MongoParamsEntity
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @var string
     */
    private $collection;

    /**
     * @var array
     */
    private $filter = [];

    /**
     * @var array
     */
    private $sort = [];

    /**
     * @var int
     */
    private $skip = 0;

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param array $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param array $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return null
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @param null $skip
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;
    }

    /**
     * @return null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param null $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}