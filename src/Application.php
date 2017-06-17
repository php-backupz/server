<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

use GeckoPackages\Silex\Services\Config\ConfigServiceProvider;
use Silex\Application as SilexApplication;

class Application extends SilexApplication
{
    public function initilize()
    {
        $app = $this;
        $app->register(new ConfigServiceProvider(), [
            'config.dir' => __DIR__.'/../app/config',
            'config.format' => '%key%.yml'
        ]);

        $app['storage'] = function () use ($app) {
            return new Storage\Storage($app);
        };
    }
}
