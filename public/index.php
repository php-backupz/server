<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Backupz\Application();
$app['debug'] = true;
$app->initilize();

$app->get('/', function () use ($app) {

    //var_dump($app['storage']->listAllFiles());

    $database = new Backupz\Backup\Database($app);
    $database->run();

    return $app->json(true);
});

$app->run();
