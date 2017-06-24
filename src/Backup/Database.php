<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

use Backupz\Application;
use Backupz\Base;
use Ifsnop\Mysqldump\Mysqldump;

class Database extends Base implements BackupInterface
{
    /**
     * Name of the database to dump
     * @var string
     */
    protected $database;

    /**
     * Instance of Mysqldump
     * @var \Ifsnop\Mysqldump\Mysqldump
     */
    protected $dump;

    /**
     * Full path of the file that will be used to dump the database to
     * @var string
     */
    protected $file;

    /**
     * The remote directory to store the backups in
     * @var string
     */
    protected $directory = 'databases';

    public function setupDump()
    {
        $this->file = tempnam($this->app['varPath'], 'dump-');
        $this->dump = new Mysqldump($this->getDSN(), $this->getUsername(), $this->getPassword());

        return $this->dump;
    }

    protected function getDSN()
    {
        return sprintf(
            'mysql:host=%s;dbname=%s',
            $this->getHost(),
            $this->database
        );
    }

    protected function getHost()
    {
        $config = $this->getConfig();
        return $config['database']['host'];
    }

    protected function getUsername()
    {
        $config = $this->getConfig();
        return $config['database']['username'];
    }

    protected function getPassword()
    {
        $config = $this->getConfig();
        return $config['database']['password'];
    }

    protected function getTmpFilename()
    {
        return str_replace($this->app['varPath'], '', $this->file);
    }

    protected function getAllDatabases()
    {
        $dbh = new \PDO('mysql:host=' . $this->getHost(), $this->getUsername(), $this->getPassword());
        $databases = [];
        foreach ($dbh->query('SHOW DATABASES') as $row) {
            // Skip backing up information_schema
            if ($row['Database'] === 'information_schema') {
                continue;
            }

            $databases[] = $row['Database'];
        }

        return $databases;
    }

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

    public function runForAll()
    {
        $databases = $this->getAllDatabases();
        foreach ($databases as $database) {
            $this->run($database);
        }
    }

    public function run($database)
    {
        $this->database = $database;
        $dump = $this->setupDump();

        try {
            $dump->start($this->file);
        } catch (\Exception $e) {
            return false;
        }

        if (file_exists($this->file)) {
            $this->save();

            return true;
        }

        return false;
    }

    public function save()
    {
        $app = $this->getContainer();
        $filename = $this->getTmpFilename();

        $app['storage']->moveToRemote(
            $filename,
            $this->directory . '/' . $this->database . '/' . time() . '.sql'
        );
    }
}
