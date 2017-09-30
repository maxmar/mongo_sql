<?php

fwrite(STDOUT, '> ');

$stdin = fopen('php://stdin', 'w');

while ($row = fgets($stdin)) {
    $row = trim($row);

    if ($row) {
        try {
            $container = require __DIR__ . '/app/bootstrap.php';

            $shellController = $container->get('mongo.shell');

            $result = $shellController->console($row);

            print_r($result);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo PHP_EOL;
    }

    fwrite(STDOUT, '> ');
}

fclose($stdin);