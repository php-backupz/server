<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

abstract class BackupBase
{
    /**
     * List all avalible backups
     * @return array
     */
    public function listAll()
    {
        $remote = $this->app['storage']->getFilesystem();
        $backups = [];

        foreach ($remote->listContents($this->directory) as $directory) {
            if ($directory['type'] !== 'dir') {
                continue;
            }

            $name = $directory['basename'];
            $databaseBackups = $this->getBackupsInDirectory($remote, $name);

            // Only show databases with backups
            if ($databaseBackups !== []) {
                $backups[$name] = $databaseBackups;
            }
        }

        return $backups;
    }

    /**
     * Find backups in a folder
     * @param  Backupz\Storage\Storage $remote Remote filesystem
     * @param  string $path Remote path
     * @return array
     */
    private function getBackupsInDirectory($remote, $path)
    {
        $files = $remote->listContents($this->directory . '/' . $path);
        $newFiles = [];
        foreach ($files as $file) {
            $time = date('d-m-y h:i', (int) $file['basename']);
            $newFiles[] = [
                'time' => $time,
                'size' => $this->getReadableFilesize($file['size'])
            ];
        }

        return $newFiles;
    }
}
