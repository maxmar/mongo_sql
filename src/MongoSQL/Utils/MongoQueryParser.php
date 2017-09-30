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
     * @param string $query
     * @param string $mainPattern
     * @param array $patternParams
     * @return MongoParamsEntity
     * @throws \Exception
     */
    public function parseQuery(string $query, string $mainPattern, array $patternParams = []): MongoParamsEntity
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
     * @param string $value
     * @param array $params
     * @throws \Exception
     */
    private function prepareFields(string $value, array $params = [])
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

            if (!key_exists('_id', $fieldsResultList)) {
                $fieldsResultList['_id'] = 0;
            }
        }

        $this->mongoParams->setFields($fieldsResultList);
    }

    /**
     * @param string $value
     * @param array $params
     */
    private function prepareCollection(string $value, array $params = [])
    {
        $this->mongoParams->setCollection($value);
    }

    /**
     * @param string $value
     * @param array $params
     */
    private function prepareWhere(string $value, array $params = [])
    {
        $logicalOperatorsList = $params['logical_operators'];

        $filtersList = $this->parseFilterStatements($value, $logicalOperatorsList, $params);

        $this->mongoParams->setFilter($filtersList);
    }

    private function parseFilterStatements(string $value, array $logicalOperatorsList, array $params = [])
    {
        if (!$logicalOperatorsList) {
            return $this->parseFilterComparison($value, $params);
        }

        $logicalOperator = array_shift($logicalOperatorsList);

        $sqlLogicalAlias = $logicalOperator['sql'];

        if (strpos($value, $sqlLogicalAlias) !== false) {
            $statements = explode($sqlLogicalAlias, $value);

            $mongoLogicalAlias = $logicalOperator['mongo'];

            $result = [];
            foreach ($statements as $statement) {
                $statement = trim($statement);
                $result[$mongoLogicalAlias][] = $this->parseFilterStatements($statement, $logicalOperatorsList, $params);
            }

            return $result;
        } else {
            return $this->parseFilterStatements($value, $logicalOperatorsList, $params);
        }
    }

    private function parseFilterComparison(string $value, array $params = [])
    {
        $comparisonPattern = $params['pattern'];
        preg_match($comparisonPattern, $value, $matches);

        if (!$matches) {
            throw new \Exception('Invalid WHERE condition: '. $value);
        }

        $fieldNameIndex = $params['field_name_index'];
        $comparisonOperatorIndex = $params['comparison_operator_index'];
        $comparisonArgumentIndex = $params['comparison_argument_index'];
        $comparisonOperatorsList = $params['comparison_operators'];

        $fieldName = $matches[$fieldNameIndex];
        $comparisonOperator = $matches[$comparisonOperatorIndex];
        $comparisonArgument = $matches[$comparisonArgumentIndex];

        if (!array_key_exists($comparisonOperator, $comparisonOperatorsList)) {
            throw new \Exception('Invalid comparison operator: '. $value);
        }

        $comparisonOperator = $comparisonOperatorsList[$comparisonOperator];

        if (strpos($comparisonArgument, '\'') !== false) {
            $comparisonArgument = str_replace('\'', '', $comparisonArgument);
        } elseif (strpos($comparisonArgument, '.') !== false) {
            $comparisonArgument = floatval($comparisonArgument);
        } else {
            $comparisonArgument = intval($comparisonArgument);
        }

        return [
            $fieldName => [
                $comparisonOperator => $comparisonArgument
            ]
        ];
    }

    /**
     * @param string $value
     * @param array $params
     * @throws \Exception
     */
    private function prepareOrderBy(string $value, array $params = [])
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
     * @param int $value
     * @param array $params
     */
    private function prepareSkip(int $value, array $params = [])
    {
        $this->mongoParams->setSkip($value);
    }

    /**
     * @param int $value
     * @param array $params
     */
    private function prepareLimit(int $value, array $params = [])
    {
        $this->mongoParams->setLimit($value);
    }
}