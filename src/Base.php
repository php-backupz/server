<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

abstract class Base
{
    /**
     * @var \Silex\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->setContainer($app);
        $this->initilize();
    }

    /**
     * Initilize the class after running the class construct
     */
    public function initilize()
    {
        // Empty
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
     * Return the config array
     * @return array
     */
    public function getConfig()
    {
        $app = $this->getContainer();
        return $app['config']['config'];
    }

    /**
     * Return the converted filesize
     * @param int $size Filesize to be converted
     * @return string
     */
    public function getReadableFilesize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
