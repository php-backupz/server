<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Console\Cron;

use Backupz\Console\Base;
use Backupz\Cron;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Base
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("cron:run")
            ->setDescription("Run the cron jobs")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeExecute($input, $output);
        $app = $this->getContainer();

        $cron = new Cron($app);
        $cron->run();

        return 1;
    }
}
