<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Backupz\Application();
$app['debug'] = true;
$app->initilize();

$app->get('/', function() use ($app) {
    // $database = new Backupz\Backup\Database($app);
    // $database->run();

    $files = new Backupz\Backup\Files($app);
    $files->runForAll();

    return $app->json(true);
});

$app->run();
