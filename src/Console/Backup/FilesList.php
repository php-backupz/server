<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Console\Backup;

use Backupz\Console\Base;
use Backupz\Backup\Files as BackupzFiles;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class FilesList extends Base
{
    protected function configure()
    {
        $this->setName("backup:files:list")
            ->setDescription("Show a table of backups available")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeExecute($input, $output);
        $backup = new BackupzFiles($this->getContainer());
        $backups = $backup->listAll();

        $tableRows = [];
        $index = 0;

        foreach ($backups as $name => $files) {
            $index++;
            foreach ($files as $file) {
                $tableRows[] = [$name, $file['time'], $file['size']];
            }

            if (count($backups) !== $index) {
                $tableRows[] = new TableSeparator();
            }
        }

        $table = new Table($output);
        $table->setHeaders(['Name', 'Time', 'Size'])
            ->setRows($tableRows)
            ->render();


        return 1;
    }
}
