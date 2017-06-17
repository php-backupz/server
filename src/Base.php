<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

abstract class Base
{
    /**
     * @var Silex\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->setContainer($app);
        $this->initilize();
    }

    public function initilize()
    {
        // Empty
    }

    protected function setContainer(Application $app)
    {
        $this->app = $app;
    }

    public function getContainer()
    {
        return $this->app;
    }

    public function getConfig()
    {
        $app = $this->getContainer();
        return $app['config']['config'];
    }
}
