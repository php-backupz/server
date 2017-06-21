<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Console\Cache;

use Backupz\Console\Base;
use Backupz\Backup\Files as BackupzFiles;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class Clear extends Base
{
    protected function configure()
    {
        $this->setName("cache:clear")
            ->setDescription("Clear the cache")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getContainer();
        $storage = $app['storage'];

        $cleared = $storage->clearCache();
        $output->writeln('<info>Cleared ' . $cleared . ' files</info>');

        return 1;
    }
}
