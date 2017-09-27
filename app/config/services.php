<?php

use Interop\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use MongoSQL\Controller\MongoShellController;
use MongoSQL\Handlers\MongoClientHandler;
use MongoSQL\Utils\MongoQueryParser;
use MongoSQL\Repository\MongoParamsEntity;

return [
    'params' => Yaml::parse(file_get_contents(__DIR__ . '/config.yml')),

    'mongo.client_handler' => function (ContainerInterface $c) {
        $mongoCredentials = $c->get('params')['mongodb'];

        $username = $mongoCredentials['username'];
        $password = $mongoCredentials['password'];
        $host = $mongoCredentials['host'];
        $port = $mongoCredentials['port'];
        $database = $mongoCredentials['database'];

        $connectionUri = sprintf('mongodb://%s:%s@%s:%d/%s', $username, $password, $host, $port, $database);

        $mongoClient = new MongoDB\Client($connectionUri);

        return new MongoClientHandler($mongoClient, $database);
    },

    'mongo.query_parser' => function() {
        $mongoParamsEntity = new MongoParamsEntity();

        return new MongoQueryParser($mongoParamsEntity);
    },

    'mongo.shell' => function (ContainerInterface $c) {
        return new MongoShellController($c);
    },
];