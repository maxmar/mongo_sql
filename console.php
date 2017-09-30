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
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        echo PHP_EOL;
    }

    fwrite(STDOUT, '> ');
}

fclose($stdin);