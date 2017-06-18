<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Backupz\Application();
$app['debug'] = true;
$app->initilize();


$files = new Backupz\Backup\Files($app);
$files->runForAll();