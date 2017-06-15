<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\VarDumperServiceProvider());
$app->register(
    new GeckoPackages\Silex\Services\Config\ConfigServiceProvider(),
    [
        'config.dir' => __DIR__.'/../app/config',
        'config.format' => '%key%.yml'
    ]
);


$app->get('/', function () use ($app) {
    $sftp = new \Backupz\Storage\SFTP($app);

    return $app->json(true);
});

$app->run();
