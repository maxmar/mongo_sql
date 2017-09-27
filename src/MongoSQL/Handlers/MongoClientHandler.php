<?php

namespace MongoSQL\Handlers;

use MongoDB\Client;
use MongoDB\Database;

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

    public function testConnection()
    {
        $cursor = $this->db->inventory->find(
            []
            ,['projection' => ['instock.qty' => 1,]]
        );

        var_dump($cursor->toArray());
    }
}