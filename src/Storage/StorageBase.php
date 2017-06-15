<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

use Silex\Application;

abstract class StorageBase
{
    /**
     * @var Silex\Application
     */
    protected $app;

    /**
     * Config for the Flysystem adapter
     * @var array
     */
    protected $config;

    /**
     * Flysystem adapter
     */
    protected $atapter;

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    protected function setContainer(Application $app)
    {
        $this->app = $app;
    }

    public function getContainer()
    {
        return $this->app;
    }

    protected function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function __construct(Application $app, array $config)
    {
        $this->setContainer($app);
        $this->setConfig($config);
        try {
            $adapter = $this->configure();
        } catch (\Exception $e) {
        }

        $this->setAdapter($adapter);

        return $adapter;
    }
}
