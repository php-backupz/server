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

class Files extends Base
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("backup:files")
            ->setDescription("Run a backup of files")
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Only run for one dir'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeExecute($input, $output);
        $name = $input->getArgument('name');
        $backup = new BackupzFiles($this->getContainer());

        if ($name !== null) {
            $backup->run($name);

            return 1;
        }

        $backup->runForAll();

        return 1;
    }
}
