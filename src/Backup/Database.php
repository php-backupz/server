<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

use Backupz\Application;
use Backupz\Base;
use Ifsnop\Mysqldump\Mysqldump;

class Database extends Base
{
    protected $file;

    protected $dump;

    public function getFile()
    {
        return $this->file;
    }

    protected function setFile($file)
    {
        $this->file = $file;
    }

    public function getDump()
    {
        return $this->dump;
    }

    protected function setDump($dump)
    {
        $this->dump = $dump;
    }

    public function initilize()
    {
        $file = tempnam($this->app['varPath'], 'dump-');
        $this->setFile($file);

        $dump = new Mysqldump($this->getDSN(), $this->getUsername(), $this->getPassword());
        $this->setDump($dump);
    }

    protected function getDSN()
    {
        return sprintf(
            'mysql:host=%s;dbname=%s',
            $this->getHost(),
            $this->getDatabase()
        );
    }

    protected function getHost()
    {
        $config = $this->getConfig();
        return $config['database']['host'];
    }

    protected function getDatabase()
    {
        $config = $this->getConfig();
        return $config['database']['database'];
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

    public function getTmpFilename()
    {
        $filename = $this->getFile();

        return str_replace($this->app['varPath'], '', $filename);
    }

    public function run()
    {
        try {
            $dump = $this->getDump();
            $dump->start($this->getFile());
        } catch (\Exception $e) {
            return false;
        }

        if (file_exists($this->getFile())) {
            $this->save();

            return true;
        }

        return false;
    }

    public function save()
    {
        $app = $this->getContainer();
        $filename = $this->getTmpFilename();
        $databaseName = $this->getDatabase();

        $app['storage']->moveToRemote($filename, 'databases/' . $databaseName . '/' . time() . '.sql');
    }
}
