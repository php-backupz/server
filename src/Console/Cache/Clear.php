<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Console\Cache;

use Backupz\Console\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class Clear extends Base
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("cache:clear")
            ->setDescription("Clear the cache")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeExecute($input, $output);
        $app = $this->getContainer();
        $storage = $app['storage'];

        $cleared = $storage->clearCache();
        $output->writeln('<info>Cleared ' . $cleared . ' files</info>');

        return 1;
    }
}
