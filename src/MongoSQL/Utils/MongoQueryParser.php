<?php

namespace MongoSQL\Utils;

use MongoSQL\Repository\MongoParamsEntity;

class MongoQueryParser
{
    /**
     * @var MongoParamsEntity
     */
    private $mongoParams;

    public function __construct(MongoParamsEntity $mongoParamsEntity)
    {
        $this->mongoParams = $mongoParamsEntity;
    }

    /**
     * @param $query
     * @param $mainPattern
     * @param array $patternParams
     * @return MongoParamsEntity
     * @throws \Exception
     */
    public function parseQuery($query, $mainPattern, $patternParams = [])
    {
        preg_match($mainPattern, $query, $matches);

        if (empty($matches)) {
            throw new \Exception('Invalid query string');
        }

        foreach ($patternParams as $param) {
            $index = $param['index'];
            $isRequired = $param['is_required'];

            if (!key_exists($index, $matches) || empty($matches[$index])) {
                if ($isRequired) {
                    throw new \Exception('Invalid query string');
                }

                continue;
            }

            $value = $matches[$index];
            $handleMethodName = $param['method'];
            $valueParams = key_exists('params', $param) ? $param['params'] : [];

            if (!method_exists($this, $handleMethodName)) {
                throw new \Exception("Internal exception: Undefined parser method {$handleMethodName}");
            }

            $this->{$handleMethodName}($value, $valueParams);
        }

        return $this->mongoParams;
    }

    /**
     * @param $value
     * @param array $params
     * @throws \Exception
     */
    private function prepareFields($value, $params = [])
    {
        $validationPattern = $params['pattern'];

        if (!preg_match($validationPattern, $value)) {
            throw new \Exception("Invalid select fields");
        }

        $value = str_replace(' ', '', $value);

        $fields = array_unique(explode(',', $value));

        $allFieldsMarker = $params['all_fields_marker'];

        $fieldsResultList = [];
        if (!in_array($allFieldsMarker, $fields)) {
            $allSubfieldsMarker = $params['all_subfields_marker'];
            $showFieldMarker = $params['show_field_marker'];

            foreach ($fields as $field) {
                $field = str_replace($allSubfieldsMarker, '', $field);
                $fieldsResultList[$field] = $showFieldMarker;
            }
        }

        $this->mongoParams->setFields($fieldsResultList);
    }

    /**
     * @param $value
     * @param array $params
     */
    private function prepareCollection($value, $params = [])
    {
        $this->mongoParams->setCollection($value);
    }

    /**
     * @param $value
     * @param array $params
     */
    private function prepareWhere($value, $params = [])
    {
        $logicalOperatorsList = $params['logical_operators'];
//        $logicalOperatorsList = $params['logical_operators'];

        $filtersList = $this->parseFilterStatements($value, $logicalOperatorsList);

        print_r($filtersList);

        // id = 10 OR name = 'Mark' AND a

//        $arrFind = array(
//            '$or' => array(
//                array(
//                    '$and'  => array(
//                        array(
//                            UI_name     => array(
//                                '$regex'    => 'andrew',
//                                '$options'  => 'i'
//                            )
//                        ),
//                        array(
//                            UI_surname  => array(
//                                '$regex' => 'mik',
//                                '$options'  => 'i'
//                            )
//                        )
//                    )
//                ),
//                array(
//                    array(
//                        UI_name  => array(
//                            '$regex' => 'mik',
//                            '$options'  => 'i'
//                        )
//                    )
//                ),
//            )
//        );


        $this->mongoParams->setFilter($value);
    }

    private function parseFilterStatements($value, $logicalOperatorsList)
    {
        if (!$logicalOperatorsList) {
            return [$value];
        }

        $logicalOperator = array_shift($logicalOperatorsList);

        $sqlLogicalAlias = $logicalOperator['sql'];

        if (strpos($value, $sqlLogicalAlias) !== false) {
            $statements = explode($sqlLogicalAlias, $value);

            $mongoLogicalAlias = $logicalOperator['mongo'];

            $result = [];
            foreach ($statements as $statement) {
                $statement = trim($statement);
                $result[$mongoLogicalAlias][] = $this->parseFilterStatements($statement, $logicalOperatorsList);
            }

            return $result;
        } else {
            return $this->parseFilterStatements($value, $logicalOperatorsList);
        }
    }

    /**
     * @param $value
     * @param array $params
     * @throws \Exception
     */
    private function prepareOrderBy($value, $params = [])
    {
        $sortParams = explode(',', $value);

        if (!$sortParams) {
            throw new \Exception('Invalid ORDER BY param');
        }

        $validationPattern = $params['pattern'];
        $indexOfField = $params['field_name_index'];
        $indexOfSortType = $params['sort_type_index'];
        $sortTypes = $params['sort_types'];

        $sortList = [];
        foreach ($sortParams as $sortParam) {
            $sortParam = trim($sortParam);

            preg_match($validationPattern, $sortParam, $matches);

            if (!$matches) {
                throw new \Exception('Invalid ORDER BY value: '. $sortParam);
            }

            $field = $matches[$indexOfField];
            $sortType = key_exists($indexOfSortType, $matches) ? $matches[$indexOfSortType] : 'ASC';

            $sortList[$field] = $sortTypes[$sortType];
        }

        $this->mongoParams->setSort($sortList);
    }

    /**
     * @param $value
     * @param array $params
     */
    private function prepareSkip($value, $params = [])
    {
        $this->mongoParams->setSkip($value);
    }

    /**
     * @param $value
     * @param array $params
     */
    private function prepareLimit($value, $params = [])
    {
        $this->mongoParams->setLimit($value);
    }
}