<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

class Log extends Base
{
    public function output($message)
    {
        $app = $this->getContainer();
        if ($app['console'] === false) {
            return false;
        }

        $app['console']->output->writeln($message);

        return true;
    }
}
