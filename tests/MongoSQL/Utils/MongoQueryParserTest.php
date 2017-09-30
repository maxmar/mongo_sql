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
}