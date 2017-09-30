<?php

namespace MongoSQL\Tests\Utils;

use MongoSQL\Repository\MongoParamsEntity;
use MongoSQL\Repository\MongoPatternsRepository;
use MongoSQL\Utils\MongoQueryParser;
use PHPUnit\Framework\TestCase;

class MongoQueryParserTest extends TestCase
{
    public function testParseQueryBasicSelect()
    {
        $paramsEntity = new MongoParamsEntity();
        $queryParser = new MongoQueryParser(clone $paramsEntity);

        $queryString = "SELECT * FROM inventory;";
        $paramsEntity->setCollection('inventory');
        $paramsEntity->setFields([]);

        $result = $queryParser->parseQuery(
            $queryString,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        $this->assertEquals($result, $paramsEntity);
    }

    public function testParseQueryFields()
    {
        $paramsEntity = new MongoParamsEntity();
        $queryParser = new MongoQueryParser(clone $paramsEntity);

        $queryString = "SELECT field, field.subfield, field.* FROM inventory;";
        $paramsEntity->setCollection('inventory');
        $paramsEntity->setFields([
            'field.subfield' => 1,
            'field' => 1,
            '_id' => 0,
        ]);

        $result = $queryParser->parseQuery(
            $queryString,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        $this->assertEquals($result, $paramsEntity);
    }

    public function testParseQueryFilters()
    {
        $paramsEntity = new MongoParamsEntity();
        $queryParser = new MongoQueryParser(clone $paramsEntity);

        $queryString = "SELECT field FROM inventory WHERE field >= 5 AND another.field = 'Some string';";
        $paramsEntity->setCollection('inventory');
        $paramsEntity->setFields([
            'field' => 1,
            '_id' => 0,
        ]);
        $paramsEntity->setFilter([
            '$and' => [
                [
                    'field' => [
                        '$gte' => 5
                    ],
                ],
                [
                    'another.field' => [
                        '$eq' => 'Some string'
                    ]
                ]
            ]
        ]);

        $result = $queryParser->parseQuery(
            $queryString,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        $this->assertEquals($result, $paramsEntity);
    }

    public function testParseQuerySort()
    {
        $paramsEntity = new MongoParamsEntity();
        $queryParser = new MongoQueryParser(clone $paramsEntity);

        $queryString = "SELECT field FROM inventory ORDER BY field DESC, another.field;";
        $paramsEntity->setCollection('inventory');
        $paramsEntity->setFields([
            'field' => 1,
            '_id' => 0,
        ]);
        $paramsEntity->setSort([
            'field' => -1,
            'another.field' => 1
        ]);

        $result = $queryParser->parseQuery(
            $queryString,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        $this->assertEquals($result, $paramsEntity);
    }

    public function testParseQuerySkip()
    {
        $paramsEntity = new MongoParamsEntity();
        $queryParser = new MongoQueryParser(clone $paramsEntity);

        $queryString = "SELECT field FROM inventory SKIP 5;";
        $paramsEntity->setCollection('inventory');
        $paramsEntity->setFields([
            'field' => 1,
            '_id' => 0,
        ]);
        $paramsEntity->setSkip(5);

        $result = $queryParser->parseQuery(
            $queryString,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        $this->assertEquals($result, $paramsEntity);
    }

    public function testParseQueryLimit()
    {
        $paramsEntity = new MongoParamsEntity();
        $queryParser = new MongoQueryParser(clone $paramsEntity);

        $queryString = "SELECT field FROM inventory LIMIT 5;";
        $paramsEntity->setCollection('inventory');
        $paramsEntity->setFields([
            'field' => 1,
            '_id' => 0,
        ]);
        $paramsEntity->setLimit(5);

        $result = $queryParser->parseQuery(
            $queryString,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        $this->assertEquals($result, $paramsEntity);
    }
}