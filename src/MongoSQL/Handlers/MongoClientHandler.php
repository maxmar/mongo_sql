<?php

namespace MongoSQL\Handlers;

use MongoDB\Client;
use MongoDB\Database;
use MongoSQL\Repository\MongoParamsEntity;

class MongoClientHandler
{
    /**
     * @var Client
     */
    private $mongoClient;

    /**
     * @var Database
     */
    private $db;

    function __construct(Client $mongoClient, $database)
    {
        $this->mongoClient = $mongoClient;
        $this->db = $this->mongoClient->selectDatabase($database);
    }

    public function query(MongoParamsEntity $mongoParamsEntity)
    {
        $collection = $mongoParamsEntity->getCollection();

        $cursor = $this->db->{$collection}->find(
            $mongoParamsEntity->getFilter(),
            [
                'projection' => $mongoParamsEntity->getFields(),
                'sort' => $mongoParamsEntity->getSort(),
                'skip' => $mongoParamsEntity->getSkip(),
                'limit' => $mongoParamsEntity->getLimit(),
            ]
        );

        $cursor->setTypeMap(['root' => 'array']);

        $result = [];
        foreach ($cursor as $document) {
            $result[] = $document;
        }

        return $result;
    }
}