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
    protected $adapter;

    /**
     * Set flysystem adapter
     * @param \League\Flysystem\AdapterInterface $adapter A configured flysystem adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return \League\Flysystem\AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set container
     * @param \Silex\Application $app
     */
    protected function setContainer(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get container
     * @return \Silex\Application
     */
    public function getContainer()
    {
        return $this->app;
    }

    /**
     * Set adapter config
     * @param array $config
     */
    protected function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Get config array
     * @return array
     */
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
