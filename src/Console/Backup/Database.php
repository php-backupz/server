<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Console\Backup;

use Backupz\Console\Base;
use Backupz\Backup\Database as BackupzDatabase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressIndicator;

class Database extends Base
{
    protected function configure()
    {
        $this->setName("backup:database")
            ->setDescription("Run a backup of the configured database")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeExecute($input, $output);
        $app = $this->getContainer();

        $progress = new ProgressIndicator($output);
        $progress->start('Starting...');
        $progress->advance();

        $backup = new BackupzDatabase($app);
        $backup->run();

        $progress->finish('Done.');

        return 1;
    }
}
