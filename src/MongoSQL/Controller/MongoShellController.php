<?php

namespace MongoSQL\Controller;

use Core\Controller;
use MongoSQL\Handlers\MongoClientHandler;
use MongoSQL\Repository\MongoPatternsRepository;

class MongoShellController extends Controller
{
    public function console($query)
    {
        if (!$query) {
            throw new \Exception('A query string cannot be empty!');
        }

        $mongoQueryParser = $this->container->get('mongo.query_parser');
        $mongoQueryParams = $mongoQueryParser->parseQuery(
            $query,
            MongoPatternsRepository::MAIN_PATTERN,
            MongoPatternsRepository::$patternParams
        );

//        var_dump($mongoQueryParams);
        return;

        /** @var MongoClientHandler $mongoClient */
        $mongoClient = $this->container->get('mongo.client_handler');
        $mongoClient->testConnection();
    }
}