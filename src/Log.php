<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

class Log extends Base
{
    /**
     * Output to the console if the app is running on the CLI
     * @param  string $message The message to output to the console
     * @return boolval
     */
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
