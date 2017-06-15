<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

use League\Flysystem\Filesystem;
use Silex\Application;

abstract class StorageBase
{
    /**
     * @var Silex\Application
     */
    protected $app;

    /**
     * Flysystem adapter
     */
    protected $atapter;

    /**
     * @var League\Flysystem\Filesystem
     */
    protected $filesystem;

    public function __construct($app)
    {
        $this->setContainer($app);
        try {
            $this->configure();
            $this->connect();
        } catch (\Exception $e) {
        }

        $filesystem = $this->getFilesystem();
        var_dump($filesystem->listContents());
    }

    protected function connect()
    {
        $filesystem = new Filesystem($this->getAdapter());
        $this->setFilesystem($filesystem);
    }

    protected function setContainer(Application $app)
    {
        $this->app = $app;
    }

    public function getContainer()
    {
        return $this->app;
    }

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function getConfig()
    {
        $app = $this->getContainer();

        return $app['config']['config'];
    }

}
