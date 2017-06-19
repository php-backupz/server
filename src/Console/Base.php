<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Console;

use Backupz\Application;
use Knp\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Base extends BaseCommand
{
    /** @var \Silex\Application */
    protected $app;

    /**
     * Get application
     * @return \Silex\Application
     */
    public function getContainer()
    {
        return $this->app;
    }

    public function __construct(Application $app)
    {
        parent::__construct();
        $this->app = $app;
    }

    protected function beforeExecute(InputInterface $input, OutputInterface $output)
    {
        $this->app["console"]->input = $input;
        $this->app["console"]->output = $output;
    }
}
