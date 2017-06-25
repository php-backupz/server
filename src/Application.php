<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

use GeckoPackages\Silex\Services\Config\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use Silex\Application as SilexApplication;

class Application extends SilexApplication
{
    /**
     * Register service providers and setup the application
     */
    public function initilize()
    {
        $app = $this;
        $app->register(new ConfigServiceProvider(), [
            'config.dir' => __DIR__ . '/../app/config',
            'config.format' => '%key%.yml'
        ])->register(new ConsoleServiceProvider(), [
            'console.name' => 'Backupz',
            'console.version' => '@DEV',
            'console.project_directory' => __DIR__.'/..'
        ]);

        $app['storage'] = function () use ($app) {
            return new Storage\Storage($app);
        };

        $app['log'] = function () use ($app) {
            return new Log($app);
        };

        $varPath = realpath(__DIR__ . '/../var');
        $app['varPath'] = function () use ($varPath) {
            return $varPath;
        };
    }
}
