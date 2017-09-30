<?php

namespace MongoSQL\Controller;

use Core\Controller;
use MongoSQL\Handlers\MongoClientHandler;
use MongoSQL\Repository\MongoParamsEntity;
use MongoSQL\Repository\MongoPatternsRepository;
use MongoSQL\Utils\MongoQueryParser;

class MongoShellController extends Controller
{
    public function console($query)
    {
        if (!$query) {
            throw new \Exception('A query string cannot be empty!');
        }

        /** @var MongoQueryParser $mongoQueryParser */
        $mongoQueryParser = $this->container->get('mongo.query_parser');

        /** @var MongoParamsEntity $mongoQueryParams */
        $mongoQueryParams = $mongoQueryParser->parseQuery(
            $query,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

        /** @var MongoClientHandler $mongoClient */
        $mongoClient = $this->container->get('mongo.client_handler');
        $queryResult = $mongoClient->query($mongoQueryParams);

        return $queryResult;
    }
}